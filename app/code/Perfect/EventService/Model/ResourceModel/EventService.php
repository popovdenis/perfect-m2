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
        $this->_init('perfect_event_service', 'entity_id');

        $this->_serializableFields = [];
    }
}