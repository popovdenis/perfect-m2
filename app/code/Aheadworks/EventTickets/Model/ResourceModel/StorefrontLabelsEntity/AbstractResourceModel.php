<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity;

use Aheadworks\EventTickets\Model\ResourceModel\AbstractResourceModel as BaseAbstractResourceModel;

/**
 * Class AbstractResourceModel
 * @package Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity
 */
abstract class AbstractResourceModel extends BaseAbstractResourceModel
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * {@inheritdoc}
     */
    protected function getArgumentsForLoading()
    {
        $arguments = parent::getArgumentsForLoading();
        $arguments['store_id'] = $this->getStoreId();
        return $arguments;
    }

    /**
     * Set store id for entity labels retrieving
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get store id for entity labels retrieving
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }
}
