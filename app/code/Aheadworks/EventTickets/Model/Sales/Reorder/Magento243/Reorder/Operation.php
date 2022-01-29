<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder;

use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Product\Loader;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Product\Loader as ProductLoader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order\Item as SalesOrderItem;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType\Pool
    as ProductTypeOperationPool;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\Error as ReorderDataError;

class Operation
{
    /**
     * @var Loader
     */
    private $productLoader;

    /**
     * @var ProductTypeOperationPool
     */
    private $productTypeOperationPool;

    /**
     * @param ProductLoader $productLoader
     * @param ProductTypeOperationPool $productTypeOperationPool
     */
    public function __construct(
        ProductLoader $productLoader,
        ProductTypeOperationPool $productTypeOperationPool
    ) {
        $this->productLoader = $productLoader;
        $this->productTypeOperationPool = $productTypeOperationPool;
    }

    /**
     * Add the list of order items to the specific quote of store
     *
     * @param Quote $cart
     * @param string $storeId
     * @param SalesOrderItem[] $orderItemList
     * @return ReorderDataError[]
     * @throws LocalizedException
     */
    public function addOrderItemListToCart(
        Quote $cart,
        string $storeId,
        array $orderItemList
    ) {
        $errorList = [];
        $listOfOrderItemByProductId = [];
        foreach ($orderItemList as $orderItem) {
            if ($orderItem->getParentItem() === null) {
                $listOfOrderItemByProductId[$orderItem->getProductId()][$orderItem->getId()] = $orderItem;
            }
        }

        foreach ($listOfOrderItemByProductId as $productId => $productOrderItemList) {
            $errorList[] = $this->addProductOrderItemListToCart(
                $cart,
                $storeId,
                $productId,
                $productOrderItemList
            );
        }

        return array_merge(...$errorList);
    }

    /**
     * Add the list of order items for the specific product to the separate quote of store
     *
     * @param Quote $cart
     * @param string $storeId
     * @param int $productId
     * @param SalesOrderItem[] $orderItemList
     * @return ReorderDataError[]
     * @throws LocalizedException
     */
    public function addProductOrderItemListToCart(
        Quote $cart,
        string $storeId,
        int $productId,
        array $orderItemList
    ) {
        $product = null;
        $productList = $this->productLoader->getProductList(
            $storeId,
            [$productId]
        );
        if (count($productList) > 0) {
            $product = reset($productList);
        }

        if ($product) {
            $operation = $this->productTypeOperationPool->getByProductTypeId(
                $product->getTypeId()
            );

            return $operation->addProductOrderItemListToCart(
                $cart,
                $product,
                $orderItemList
            );
        }

        return [];
    }
}
