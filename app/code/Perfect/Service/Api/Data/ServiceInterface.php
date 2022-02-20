<?php

namespace Perfect\Service\Api\Data;

/**
 * Interface ServiceInterface
 *
 * @package Perfect\Service\Api\Data
 */
interface ServiceInterface
{
    /**
     * Constants defined for keys of array
     */
    const ID = 'entity_id';
    const SERVICE_NAME = 'service_name';
    const SERVICE_DURATION = 'service_duration';
    const SERVICE_PRICE = 'service_price';
    const SERVICE_PRICE_FROM = 'service_price_from';
    const SERVICE_PRICE_TO = 'service_price_to';

    /**
     * Entity ID setter.
     *
     * @param string $entityId
     *
     * @return self
     */
    public function setId($entityId): ServiceInterface;

    /**
     * ID getter.
     *
     * @return string|null
     */
    public function getId();

    /**
     * Service Name setter.
     *
     * @param string $serviceName
     *
     * @return self
     */
    public function setServiceName($serviceName): ServiceInterface;

    /**
     * Service name getter.
     *
     * @return string|null
     */
    public function getServiceName();

    /**
     * Service duration setter.
     *
     * @param string $serviceDuration
     *
     * @return self
     */
    public function setServiceDuration($serviceDuration): ServiceInterface;

    /**
     * Service duration getter.
     *
     * @return string|null
     */
    public function getServiceDuration();

    /**
     * Service base price setter.
     *
     * @param string $serviceDuration
     *
     * @return self
     */
    public function setServicePrice($price): ServiceInterface;

    /**
     * Service base price getter.
     *
     * @return string|null
     */
    public function getServicePrice();

    /**
     * Service price from range setter.
     *
     * @param string $priceFrom
     *
     * @return self
     */
    public function setServicePriceFrom($priceFrom): ServiceInterface;

    /**
     * Service price from range getter.
     *
     * @return string|null
     */
    public function getServicePriceFrom();

    /**
     * Service price to range setter.
     *
     * @param string $priceTo
     *
     * @return self
     */
    public function setServicePriceTo($priceTo): ServiceInterface;

    /**
     * Service price to range getter.
     *
     * @return string|null
     */
    public function getServicePriceTo();
}