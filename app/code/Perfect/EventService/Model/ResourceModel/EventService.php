<?php

namespace Perfect\EventService\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class EventService
 *
 * @package Perfect\EventService\Model\ResourceModel
 */
class EventService extends AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('perfect_service', 'entity_id');

        $this->_serializableFields = [];
    }

    /**
     * @param array $ids
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByIds(array $ids)
    {
        $select = $this->getConnection()->select();
        $select->from(['e' => $this->getMainTable()])
            ->where('e.entity_id IN (?)', implode(',', $ids));

        $delete = $this->getConnection()->deleteFromSelect($select, 'e');

        $this->getConnection()->query($delete);
    }
}