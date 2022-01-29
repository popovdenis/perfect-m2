<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View;

use Magento\Catalog\Model\Product;

/**
 * Interface LayoutProcessorInterface
 *
 * @package Magento\Checkout\Block\Checkout
 */
interface LayoutProcessorInterface
{
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @param Product $product
     * @return array
     */
    public function process($jsLayout, $product);
}
