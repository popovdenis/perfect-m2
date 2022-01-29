<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver;

use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product\TypePool;
use Magento\Quote\Model\Quote\Item;

/**
 * Class Product
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver
 */
class Product
{
    /**
     * @var TypePool
     */
    private $typePool;

    /**
     * @param TypePool $typePool
     */
    public function __construct(TypePool $typePool)
    {
        $this->typePool = $typePool;
    }

    /**
     * Resolve product by quote item
     *
     * @param Item $item
     * @return \Magento\Catalog\Model\Product
     */
    public function resolve($item)
    {
        $product = $item->getProduct();
        $productType = $product->getTypeId();
        if ($this->typePool->hasResolver($productType)) {
            return $this->typePool->getResolver($productType)->resolve($item);
        }
        return $product;
    }
}
