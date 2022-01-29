<?php
namespace Aheadworks\EventTickets\Plugin\Model;

use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\AttributeResolver;
use Magento\Catalog\Model\Product;

/**
 * Class ProductPlugin
 * @package Aheadworks\EventTickets\Plugin\Model
 */
class ProductPlugin
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
     * Add Event Tickets information to the product's extension attributes
     *
     * @param Product $product
     * @return Product
     */
    public function afterLoad(Product $product)
    {
        $productExtension = $product->getExtensionAttributes();
        $productExtension->setAwEtExclusiveProduct($this->attributeResolver->resolveExclusive($product));
        $product->setExtensionAttributes($productExtension);

        return $product;
    }
}
