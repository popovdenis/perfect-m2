<?php

namespace Perfect\EventService\Model;

use Magento\Framework\Model\AbstractModel;
use Perfect\EventService\Api\Data\EventServiceInterface;

/**
 * Class EventService
 *
 * @package Perfect\EventService\Model
 */
class EventService extends AbstractModel implements EventServiceInterface
{
    /**
     * Model cache tag for clear cache in after save and after delete.
     *
     * @const string
     */
    const CACHE_TAG = 'perfect_event_service';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'perfect_event_service';

    /**
     * Model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Perfect\EventService\Model\ResourceModel\EventService::class);
    }

    /**
     * @inheritdoc
     */
    public function setId($entityId): EventServiceInterface
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
    public function setServiceName($serviceName): EventServiceInterface
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
    public function setServiceQuantity($serviceQuantity): EventServiceInterface
    {
        return $this->setData(self::SERVICE_QUANTITY, $serviceQuantity);
    }

    /**
     * @inheritdoc
     */
    public function getServiceQuantity()
    {
        return $this->getData(self::SERVICE_QUANTITY);
    }

    /**
     * @inheritdoc
     */
    public function setEventId($eventId): EventServiceInterface
    {
        return $this->setData(self::EVENT_ID, $eventId);
    }

    /**
     * @inheritdoc
     */
    public function getEventId()
    {
        return $this->getData(self::EVENT_ID);
    }
}