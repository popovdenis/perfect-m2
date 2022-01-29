<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ConverterInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class DeadlineCorrection
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter
 */
class DeadlineCorrection implements ConverterInterface
{
    /**
     * @var DeadlineCorrectionInterfaceFactory
     */
    private $deadlineCorrectionFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param DeadlineCorrectionInterfaceFactory $optionsFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        DeadlineCorrectionInterfaceFactory $optionsFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->deadlineCorrectionFactory = $optionsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritDoc
     */
    public function arrayToDataModel(array $deadlineCorrectionData)
    {
        /** @var DeadlineCorrectionInterface $dataModel */
        $dataModel = $this->deadlineCorrectionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataModel,
            $deadlineCorrectionData,
            DeadlineCorrectionInterface::class
        );

        return $dataModel;
    }

    /**
     * @inheritDoc
     */
    public function dataModelToArray($dataModel)
    {
        return $this->dataObjectProcessor->buildOutputDataArray(
            $dataModel,
            DeadlineCorrectionInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function fromFormData($data)
    {
        $prepared = (array)$this->resolveItemValue(
            $data,
            ProductRecurringScheduleInterface::SELLING_DEADLINE_CORRECTION
        );

        $data[ProductRecurringScheduleInterface::SELLING_DEADLINE_CORRECTION] = $this->arrayToDataModel($prepared);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function toFormData($data)
    {
        $deadlineCorrection = $this->resolveItemValue(
            $data,
            ProductRecurringScheduleInterface::SELLING_DEADLINE_CORRECTION
        );
        if (!is_array($deadlineCorrection)) {
            $data[ProductRecurringScheduleInterface::SELLING_DEADLINE_CORRECTION] = [];
        }

        return $data;
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
