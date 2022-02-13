<?php

namespace Perfect\Event\Model;

use Magento\Framework\Model\AbstractModel;
use Perfect\Event\Api\Data\EventInterface;

/**
 * Class Event
 *
 * @package Perfect\Event\Model
 */
class Event extends AbstractModel implements EventInterface
{
    /**
     * Model cache tag for clear cache in after save and after delete.
     *
     * @const string
     */
    const CACHE_TAG = 'perfect_event';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'perfect_event';

    /**
     * Model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Perfect\Event\Model\ResourceModel\Event::class);
    }

    /**
     * @inheritdoc
     */
    public function setId($entityId): EventInterface
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setServiceName($serviceName): EventInterface
    {
        return $this->setData(self::SERVICE_NAME, $serviceName);
    }

    /**
     * @inheritdoc
     */
    public function getServiceName()
    {
        return $this->getData(self::SERVICE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description): EventInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setStartedAt($startedAt): EventInterface
    {
        return $this->setData(self::STARTED_AT, $startedAt);
    }

    /**
     * @inheritdoc
     */
    public function getStartedAt()
    {
        return $this->getData(self::STARTED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setFinishedAt($finishedAt): EventInterface
    {
        return $this->setData(self::FINISHED_AT, $finishedAt);
    }

    /**
     * @inheritdoc
     */
    public function getFinishedAt()
    {
        return $this->getData(self::FINISHED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setEnabled($flag): EventInterface
    {
        return $this->setData(self::ENABLED, $flag);
    }

    /**
     * @inheritdoc
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function setWorkerId($workerId): EventInterface
    {
        return $this->setData(self::WORKER_ID, $workerId);
    }

    /**
     * @inheritdoc
     */
    public function getWorkerId()
    {
        return $this->getData(self::WORKER_ID);
    }
}
