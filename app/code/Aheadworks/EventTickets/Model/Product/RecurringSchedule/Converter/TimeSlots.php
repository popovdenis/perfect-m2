<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ConverterInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class TimeSlots
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter
 */
class TimeSlots implements ConverterInterface
{
    /**
     * @var TimeSlotInterfaceFactory
     */
    private $timeSlotFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param TimeSlotInterfaceFactory $timeSlotsFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        TimeSlotInterfaceFactory $timeSlotsFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->timeSlotFactory = $timeSlotsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritDoc
     */
    public function arrayToDataModel(array $timeSlots)
    {
        $data = [];

        foreach ($timeSlots as $timeSlot) {
            /** @var TimeSlotInterface $dataModel */
            $dataModel = $this->timeSlotFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $dataModel,
                $timeSlot,
                TimeSlotInterface::class
            );
            $data[] = $dataModel;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function dataModelToArray($timeSlots)
    {
        $data = [];
        foreach ($timeSlots as $timeSlot) {
            $data[] = $this->dataObjectProcessor->buildOutputDataArray(
                $timeSlot,
                TimeSlotInterface::class
            );
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fromFormData($data)
    {
        $prepared = [];
        $timeSlots = (array)$this->resolveItemValue($data, ProductRecurringScheduleInterface::TIME_SLOTS);

        foreach ($timeSlots as $timeSlot) {
            $timeSlot[TimeSlotInterface::START_TIME] = $this->prepareOptionValue(
                TimeSlotInterface::START_TIME,
                $this->resolveItemValue($timeSlot, TimeSlotInterface::START_TIME)
            );
            $timeSlot[TimeSlotInterface::END_TIME] = $this->prepareOptionValue(
                TimeSlotInterface::END_TIME,
                $this->resolveItemValue($timeSlot, TimeSlotInterface::END_TIME)
            );
            $prepared[] = $timeSlot;
        }

        $data[ProductRecurringScheduleInterface::TIME_SLOTS] = $prepared;

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function toFormData($data)
    {
        return $data;
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
            case TimeSlotInterface::END_TIME:
            case TimeSlotInterface::START_TIME:
                $date = new \DateTime($value, $timezone);
                $newValue = $date->format(StdlibDateTime::DATETIME_PHP_FORMAT);
                break;
            default:
                $newValue = $value;
        }

        return $newValue;
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
