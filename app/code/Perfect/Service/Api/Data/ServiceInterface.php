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
    const SERVICE_DURATION_H = 'service_duration_h';
    const SERVICE_DURATION_M = 'service_duration_m';
    const SERVICE_PRICE_RANGE = 'is_price_range';
    const SERVICE_PRICE_FROM = 'service_price_from';
    const SERVICE_PRICE_TO = 'service_price_to';
    const EMPLOYEE_POSITION_ID = 'employee_position_id';
    const EMPLOYEE_LEVEL_ID = 'employee_level_id';

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
     * Service duration in hours setter.
     *
     * @param string $serviceDurationH
     *
     * @return self
     */
    public function setServiceDurationH($serviceDurationH): ServiceInterface;

    /**
     * Service duration in hours getter.
     *
     * @return string|null
     */
    public function getServiceDurationH();

    /**
     * Service duration in minutes setter.
     *
     * @param string $serviceDurationM
     *
     * @return self
     */
    public function setServiceDurationM($serviceDurationM): ServiceInterface;

    /**
     * Service duration in minutes getter.
     *
     * @return string|null
     */
    public function getServiceDurationM();

    /**
     * Price range flag setter.
     *
     * @param string $serviceDuration
     *
     * @return self
     */
    public function setIsPriceRange($flag): ServiceInterface;

    /**
     * Price range flag getter.
     *
     * @return string|null
     */
    public function getIsPriceRange();

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

    /**
     * Employee position setter.
     *
     * @param string $positionId
     *
     * @return self
     */
    public function setEmployeePositionId($positionId): ServiceInterface;

    /**
     * Employee position getter.
     *
     * @return string|null
     */
    public function getEmployeePositionId();

    /**
     * Employee level setter.
     *
     * @param string $level
     *
     * @return self
     */
    public function setEmployeeLevelId($level): ServiceInterface;

    /**
     * Employee level getter.
     *
     * @return string|null
     */
    public function getEmployeeLevelId();
}