<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;

/**
 * Interface ProductBuilderProcessorInterface
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
interface ProductBuilderProcessorInterface
{
    /**
     * Prepare product data
     *
     * @param ProductInterface|Product $product
     * @param AdditionalProductRenderInterface $productRender
     * @return void
     */
    public function build($product, $productRender);
}
