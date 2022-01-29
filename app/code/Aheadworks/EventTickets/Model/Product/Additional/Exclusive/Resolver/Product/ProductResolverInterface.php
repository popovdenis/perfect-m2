<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product;

use Magento\Quote\Model\Quote\Item;

/**
 * Class ProductResolverInterface
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product
 */
interface ProductResolverInterface
{
    /**
     * Resolve product by quote item
     *
     * @param Item $item
     * @return \Magento\Catalog\Model\Product
     */
    public function resolve($item);
}
