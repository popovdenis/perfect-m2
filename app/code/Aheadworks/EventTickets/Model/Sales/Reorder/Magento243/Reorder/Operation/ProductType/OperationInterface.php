<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType;

use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product;
use Magento\Sales\Model\Order\Item as SalesOrderItem;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\Error as ReorderDataError;

interface OperationInterface
{
    /**
     * Add the list of order items for the specific product to the separate quote of store
     *
     * @param Quote $cart
     * @param Product $product
     * @param SalesOrderItem[] $orderItemList
     * @return ReorderDataError[]
     */
    public function addProductOrderItemListToCart(
        Quote $cart,
        Product $product,
        array $orderItemList
    );
}
