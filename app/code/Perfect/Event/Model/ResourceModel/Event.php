<?php
namespace Perfect\Event\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Event
 *
 * @package Perfect\Event\Model\ResourceModel
 */
class Event extends AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('perfect_event', 'id');

        $this->_serializableFields = [];
    }
}