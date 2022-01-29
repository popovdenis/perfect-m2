<?php
namespace Aheadworks\EventTickets\Model\Import\Processor;

use Magento\Catalog\Model\Product;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\EventTickets\Model\Import\Processor
 */
interface ProcessorInterface
{
    /**
     * Prepare data for import
     *
     * @param array $rowData
     * @param Product $entity
     * @return array
     */
    public function processData($rowData, $entity);
}
