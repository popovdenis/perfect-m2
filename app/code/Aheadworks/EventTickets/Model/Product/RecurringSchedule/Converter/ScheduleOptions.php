<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ConverterInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class ScheduleOptions
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter
 */
class ScheduleOptions implements ConverterInterface
{
    /**
     * @var ScheduleOptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ScheduleOptionInterfaceFactory $optionsFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ScheduleOptionInterfaceFactory $optionsFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->optionFactory = $optionsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritDoc
     */
    public function arrayToDataModel(array $options)
    {
        $data = [];

        foreach ($options as $option) {
            /** @var ScheduleOptionInterface $dataModel */
            $dataModel = $this->optionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $dataModel,
                $option,
                ScheduleOptionInterface::class
            );
            $data[] = $dataModel;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function dataModelToArray($options)
    {
        $data = [];
        foreach ($options as $option) {
            $data[] = $this->dataObjectProcessor->buildOutputDataArray(
                $option,
                ScheduleOptionInterface::class
            );
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fromFormData($data)
    {
        $preparedOptions = [];
        $recurringScheduleType = $this->resolveItemValue($data, ProductRecurringScheduleInterface::TYPE);
        $itemKey = $this->getItemKey($recurringScheduleType);
        $options = (array)$this->resolveItemValue($data, $itemKey);

        foreach ($options as $key => $optionValue) {
            $optionValue = $this->prepareOptionValue($key, $optionValue);
            $preparedOptions[] = [
                ScheduleOptionInterface::KEY => $key,
                ScheduleOptionInterface::VALUE => $optionValue
            ];
        }

        unset($data[$itemKey]);
        $data[ProductRecurringScheduleInterface::SCHEDULE_OPTIONS] = $preparedOptions;

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function toFormData($data)
    {
        $options = (array)$this->resolveItemValue($data, ProductRecurringScheduleInterface::SCHEDULE_OPTIONS);

        if (!empty($options)) {
            $scheduleType = $this->resolveItemValue($data, ProductRecurringScheduleInterface::TYPE);
            $itemKey = $this->getItemKey($scheduleType);
            $preparedOptions = [];

            foreach ($options as $option) {
                $preparedOptions = array_merge(
                    $preparedOptions,
                    [$option[ScheduleOptionInterface::KEY] => $option[ScheduleOptionInterface::VALUE]]
                );
            }
            if (!empty($preparedOptions)) {
                $data[$itemKey] = $preparedOptions;
            }
        }

        return $data;
    }

    /**
     * Convert to form data
     *
     * @param ScheduleOptionInterface[] $scheduleOptions
     * @return array
     */
    public function toFlatArray($scheduleOptions)
    {
        $preparedOptions = [];

        /** @var ScheduleOptionInterface $option */
        foreach ($scheduleOptions as $option) {
            $preparedOptions = array_merge(
                $preparedOptions,
                [$option->getKey() => $option->getValue()]
            );
        }

        return $preparedOptions;
    }

    /**
     * Prepare option value
     *
     * @param string $key
     * @param string|array $value
     * @return string|array
     */
    private function prepareOptionValue($key, $value)
    {
        $timezone = new \DateTimeZone('UTC');

        switch ($key) {
            case ScheduleOptionInterface::END_DATE:
            case ScheduleOptionInterface::START_DATE:
                $date = new \DateTime($value, $timezone);
                $newValue = $date->format(StdlibDateTime::DATETIME_PHP_FORMAT);
                break;
            case ScheduleOptionInterface::MONTH_DAYS:
                $newValue = is_string($value) ? explode(',', $value) : $value;
                $newValue = array_filter($newValue, function ($dayNumber) {
                    return (int)$dayNumber && $dayNumber <= 31;
                });
                break;
            default:
                $newValue = $value;
        }

        return $newValue;
    }

    /**
     * Retrieve item key
     *
     * @param string $recurringScheduleType
     * @return string
     */
    private function getItemKey($recurringScheduleType)
    {
        return $recurringScheduleType . '_options';
    }

    /**
     * Resolve item value
     *
     * @param array|null $data
     * @param string $key
     * @return null|mixed
     */
    private function resolveItemValue($data, $key)
    {
        return is_array($data) && isset($data[$key])
            ? $data[$key]
            : null;
    }
}
