<?php
namespace Aheadworks\EventTickets\Model\Import;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class StoreProcessor
 * @package Aheadworks\EventTickets\Model\Import
 */
class StoreProcessor
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreInterface[]
     */
    private $stores;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        $this->stores = $this->storeManager->getStores();
    }

    /**
     * Change store label to id
     *
     * @param array $array
     * @param string $fieldName
     * @return array
     */
    public function changeStoreLabelToId($array, $fieldName = 'store_id')
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->changeStoreLabelToId($value);
            } elseif ($key == $fieldName) {
                $array[$key] = $this->getStoreIdByName($value, $fieldName);
            }
        }
        return $array;
    }

    /**
     * Retrieve store id by name
     *
     * @param string $storeName
     * @return int
     */
    private function getStoreIdByName($storeName)
    {
        foreach ($this->stores as $store) {
            if ($store->getName() == $storeName) {
                return $store->getId();
            }
        }
        return 0;
    }
}
