<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

/**
 * Class Common
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
class Common implements ProductBuilderProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function build($product, $productRender)
    {
        $product->setDoNotUseCategoryId(true);
        $productRender
            ->setId($product->getId())
            ->setKey(uniqid())
            ->setQty(0)
            ->setSku($product->getSku())
            ->setName($product->getName())
            ->setShortDescription($product->getShortDescription())
            ->setUrl($product->getUrlModel()->getUrl($product))
            ->setType($product->getTypeId())
            ->setIsSalable($product->isSalable());
    }
}
