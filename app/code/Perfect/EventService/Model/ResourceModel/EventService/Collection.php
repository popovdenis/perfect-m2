<?php

namespace Perfect\EventService\Model\ResourceModel\EventService;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Perfect\EventService\Model\ResourceModel\EventService
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            \Perfect\EventService\Model\EventService::class,
            \Perfect\EventService\Model\ResourceModel\EventService::class
        );
    }
}