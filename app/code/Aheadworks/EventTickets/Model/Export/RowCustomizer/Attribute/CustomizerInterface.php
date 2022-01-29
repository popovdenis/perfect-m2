<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute;

/**
 * Interface CustomizerInterface
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute
 */
interface CustomizerInterface
{
    /**
     * Prepare data for export
     *
     * @param array $eventTicketsAttributesProductsData
     * @return array
     */
    public function prepareData($eventTicketsAttributesProductsData);

    /**
     * Retrieve headers columns
     *
     * @return array
     */
    public function getHeaderColumns();
}
