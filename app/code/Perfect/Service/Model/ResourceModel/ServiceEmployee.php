<?php

namespace Perfect\Service\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ServiceEmployee
 *
 * @package Perfect\Service\Model\ResourceModel
 */
class ServiceEmployee extends AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('perfect_service_employee', 'entity_id');

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
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable())
            ->where('service_id = ?', $service->getServiceId());

        $query = $this->getConnection()->deleteFromSelect(
            $select,
            $this->getMainTable()
        );
        $this->getConnection()->query($query);

        $this->getConnection()->insertMultiple($this->getMainTable(), $items);
    }
}