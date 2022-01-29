<?php
namespace Aheadworks\EventTickets\Model\StorefrontLabelsEntity;

use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractCollection;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractResourceModel;

/**
 * Class Repository
 * @package Aheadworks\EventTickets\Model\StorefrontLabelsEntity
 */
abstract class AbstractRepository
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Apply store id to entity with store view - specific data
     *
     * @param AbstractCollection|AbstractResourceModel $object
     * @param int|null $storeId
     * @return mixed
     */
    protected function applyStoreIdToObject($object, $storeId)
    {
        $storeId = isset($storeId) ? $storeId : $this->storeManager->getStore()->getId();
        $object->setStoreId($storeId);
        return $object;
    }
}
