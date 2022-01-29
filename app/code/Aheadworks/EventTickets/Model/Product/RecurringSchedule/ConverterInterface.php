<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule;

use Magento\Framework\DataObject;

/**
 * Interface ConverterInterface
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
interface ConverterInterface
{
    /**
     * Convert array to data model
     *
     * @param array $data
     * @return DataObject|DataObject[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function arrayToDataModel(array $data);

    /**
     * Convert data model to array
     *
     * @param DataObject|DataObject[] $dataModel
     * @return array
     */
    public function dataModelToArray($dataModel);

    /**
     * Convert from form data
     *
     * @param array $data
     * @return array
     */
    public function fromFormData($data);

    /**
     * Convert to form data
     *
     * @param DataObject|array $dataModel
     * @return array
     */
    public function toFormData($dataModel);
}
