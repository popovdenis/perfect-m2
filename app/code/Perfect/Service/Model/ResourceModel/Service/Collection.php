<?php

namespace Perfect\Service\Model\ResourceModel\Service;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Perfect\Service\Model\ResourceModel\Service
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            \Perfect\Service\Model\Service::class,
            \Perfect\Service\Model\ResourceModel\Service::class
        );
    }
}