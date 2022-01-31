<?php

namespace Perfect\Event\Model\ResourceModel\Event;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Perfect\Event\Model\ResourceModel\Event
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            \Perfect\Event\Model\Event::class,
            \Perfect\Event\Model\ResourceModel\Event::class
        );
    }
}
