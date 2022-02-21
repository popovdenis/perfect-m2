<?php

namespace Perfect\Service\Model;

use Magento\Framework\Model\AbstractModel;
use Perfect\Service\Api\Data\ServiceInterface;

/**
 * Class Service
 *
 * @package Perfect\Service\Model
 */
class Service extends AbstractModel implements ServiceInterface
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
        $this->_init(\Perfect\Service\Model\ResourceModel\Service::class);
    }

    /**
     * @inheritdoc
     */
    public function setId($entityId): ServiceInterface
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
    public function setServiceName($serviceName): ServiceInterface
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
    public function setServiceDurationH($serviceDurationH): ServiceInterface
    {
        return $this->setData(self::SERVICE_DURATION_H, $serviceDurationH);
    }

    /**
     * @inheritdoc
     */
    public function getServiceDurationH()
    {
        return $this->getData(self::SERVICE_DURATION_H);
    }

    /**
     * @inheritdoc
     */
    public function setServiceDurationM($serviceDurationM): ServiceInterface
    {
        return $this->setData(self::SERVICE_DURATION_M, $serviceDurationM);
    }

    /**
     * @inheritdoc
     */
    public function getServiceDurationM()
    {
        return $this->getData(self::SERVICE_DURATION_M);
    }

    /**
     * @inheritdoc
     */
    public function setServicePrice($price): ServiceInterface
    {
        return $this->setData(self::SERVICE_PRICE, $price);
    }

    /**
     * @inheritdoc
     */
    public function getServicePrice()
    {
        return $this->getData(self::SERVICE_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setServicePriceFrom($priceFrom): ServiceInterface
    {
        return $this->setData(self::SERVICE_PRICE_FROM, $priceFrom);
    }

    /**
     * @inheritdoc
     */
    public function getServicePriceFrom()
    {
        return $this->getData(self::SERVICE_PRICE_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setServicePriceTo($priceTo): ServiceInterface
    {
        return $this->setData(self::SERVICE_PRICE_TO, $priceTo);
    }

    /**
     * @inheritdoc
     */
    public function getServicePriceTo()
    {
        return $this->getData(self::SERVICE_PRICE_TO);
    }
}