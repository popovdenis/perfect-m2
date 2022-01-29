<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class Converter
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
class Converter implements ConverterInterface
{
    /**
     * @var ProductRecurringScheduleInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ConverterInterface[]
     */
    private $converters;

    /**
     * @param ProductRecurringScheduleInterfaceFactory $optionsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param array $converters
     */
    public function __construct(
        ProductRecurringScheduleInterfaceFactory $optionsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        array $converters = []
    ) {
        $this->optionFactory = $optionsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->converters = $converters;
    }

    /**
     * @inheritDoc
     */
    public function arrayToDataModel(array $data)
    {
        /** @var ProductRecurringScheduleInterface $dataModel */
        $dataModel = $this->optionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataModel,
            $data,
            ProductRecurringScheduleInterface::class
        );

        return $dataModel;
    }

    /**
     * @inheritDoc
     */
    public function dataModelToArray($dataModel)
    {
        $data = $this->dataObjectProcessor->buildOutputDataArray(
            $dataModel,
            ProductRecurringScheduleInterface::class
        );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fromFormData($formData)
    {
        $recurringScheduleData = $this->resolveItemValue(
            $formData,
            ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE,
            []
        );
        $recurringScheduleData[ProductRecurringScheduleInterface::TYPE] = $this->resolveItemValue(
            $formData,
            ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE_TYPE,
            ''
        );
        $recurringScheduleData[ProductRecurringScheduleInterface::PRODUCT_ID] = $this->resolveItemValue(
            $formData,
            'current_product_id'
        );
        if (!$this->resolveItemValue($recurringScheduleData, ProductRecurringScheduleInterface::ID)) {
            unset($recurringScheduleData[ProductRecurringScheduleInterface::ID]);
        }

        foreach ($this->converters as $converter) {
            if ($converter instanceof ConverterInterface) {
                $recurringScheduleData = $converter->fromFormData($recurringScheduleData);
            }
        }

        return $this->arrayToDataModel($recurringScheduleData);
    }

    /**
     * @inheritDoc
     */
    public function toFormData($recurringSchedule)
    {
        $data = $this->dataModelToArray($recurringSchedule);

        foreach ($this->converters as $converter) {
            if ($converter instanceof ConverterInterface) {
                $data = $converter->toFormData($data);
            }
        }

        return $data;
    }

    /**
     * Resolve item value
     *
     * @param array|null $data
     * @param string $key
     * @param mixed $default
     * @return null|mixed
     */
    private function resolveItemValue($data, $key, $default = null)
    {
        return is_array($data) && isset($data[$key])
            ? $data[$key]
            : $default;
    }
}
