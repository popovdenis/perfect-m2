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
    const SERVICE_QUANTITY = 'service_quantity';
    const EVENT_ID = 'event_id';

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
     * Service Quantity setter.
     *
     * @param string $serviceQuantity
     *
     * @return self
     */
    public function setServiceQuantity($serviceQuantity): ServiceInterface;

    /**
     * Service Quantity getter.
     *
     * @return string|null
     */
    public function getServiceQuantity();

    /**
     * Event ID setter.
     *
     * @param string $eventId
     *
     * @return self
     */
    public function setEventId($eventId): ServiceInterface;

    /**
     * Event ID getter.
     *
     * @return string|null
     */
    public function getEventId();
}