<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;

/**
 * Interface ConfigurationInterface
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option
 */
interface ConfigurationInterface
{
    /**
     * Get options array
     *
     * @param ProductInterface|Product $product
     * @return array
     */
    public function getOptions($product);
}
