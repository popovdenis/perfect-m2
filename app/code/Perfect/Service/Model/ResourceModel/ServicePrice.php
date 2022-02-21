<?php

namespace Perfect\Service\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ServicePrice
 *
 * @package Perfect\Service\Model\ResourceModel
 */
class ServicePrice extends AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('perfect_service_price', 'entity_id');

        $this->_serializableFields = [];
    }

    /**
     * Save multiple items.
     *
     * @param \Magento\Framework\Model\AbstractModel $service
     * @param array                                  $items
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertMultiple(\Magento\Framework\Model\AbstractModel $service, array $items)
    {
        $this->deleteServicePrices($service->getId());

        $this->getConnection()->insertMultiple($this->getMainTable(), $items);
    }

    public function getServicePrices($serviceId)
    {
        return $this->getConnection()->fetchAll($this->getServicePricesQuery($serviceId));
    }

    public function deleteServicePrices($serviceId)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getServicePricesQuery($serviceId),
            $this->getMainTable()
        );
        $this->getConnection()->query($query);
    }

    protected function getServicePricesQuery($serviceId)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable())
            ->where('service_id = ?', $serviceId);

        return $select;
    }
}