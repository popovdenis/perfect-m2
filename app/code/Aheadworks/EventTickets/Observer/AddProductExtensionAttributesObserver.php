<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\AttributeResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddProductExtensionAttributesObserver
 * @package Aheadworks\EventTickets\Observer
 */
class AddProductExtensionAttributesObserver implements ObserverInterface
{
    /**
     * @var AttributeResolver
     */
    private $attributeResolver;

    /**
     * @param AttributeResolver $attributeResolver
     */
    public function __construct(
        AttributeResolver $attributeResolver
    ) {
        $this->attributeResolver = $attributeResolver;
    }

    /**
     * Add Extension attributes to products in collection
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Collection $productCollection */
        $productCollection = $observer->getData('collection');
        /** @var Product $product */
        foreach ($productCollection->getItems() as $product) {
            $productExtension = $product->getExtensionAttributes();
            $productExtension->setAwEtExclusiveProduct($this->attributeResolver->resolveExclusive($product));
            $product->setExtensionAttributes($productExtension);
        }
    }
}
