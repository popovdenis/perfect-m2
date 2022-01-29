<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor\EndDate;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductUpdater
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor\EndDate
 */
class ProductUpdater
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductAction
     */
    private $productAction;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ProductAction $productAction
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductAction $productAction
    ) {
        $this->storeManager = $storeManager;
        $this->productAction = $productAction;
    }

    /**
     * Disable products
     *
     * @param array $productIds
     */
    public function disableProducts($productIds)
    {
        if (is_array($productIds) && !empty($productIds)) {
            $storeIds = array_keys($this->storeManager->getStores(true));
            $updateAttributes[ProductInterface::STATUS] = ProductStatus::STATUS_DISABLED;

            foreach ($storeIds as $storeId) {
                $this->productAction->updateAttributes($productIds, $updateAttributes, $storeId);
            }
        }
    }
}
