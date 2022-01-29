<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Item as SalesOrderItem;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Product\Loader
    as ProductLoader;

class Resolver
{
    /**
     * @var Loader
     */
    private $productLoader;

    /**
     * @param ProductLoader $productLoader
     */
    public function __construct(
        ProductLoader $productLoader
    ) {
        $this->productLoader = $productLoader;
    }

    /**
     * Retrieve the list of unavailable product id for the given list of order items from the store
     *
     * @param SalesOrderItem[] $orderItemList
     * @param string $storeId
     * @return int[]
     * @throws LocalizedException
     */
    public function getUnavailableProductIdList(array $orderItemList, string $storeId)
    {
        $orderItemProductIdList = [];
        foreach ($orderItemList as $orderItem) {
            if ($orderItem->getParentItem() === null) {
                $orderItemProductIdList[] = $orderItem->getProductId();
            }
        }

        $availableProductList = $this->productLoader->getProductList($storeId, $orderItemProductIdList);

        return array_diff(
            $orderItemProductIdList,
            array_keys($availableProductList)
        );
    }
}
