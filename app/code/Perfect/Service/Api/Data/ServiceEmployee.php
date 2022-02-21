<?php

namespace Perfect\Service\Api\Data;

/**
 * Interface ServiceEmployee
 *
 * @package Perfect\Service\Api\Data
 */
interface ServiceEmployee
{
    /**
     * Constants defined for keys of array
     */
    const ID = 'entity_id';
    const EMPLOYEE_ID = 'employee_id';
    const SERVICE_ID = 'service_id';
    const SERVICE_DURATION_H = 'service_duration_h';
    const SERVICE_DURATION_M = 'service_duration_m';

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
     * Employee ID setter.
     *
     * @param string $employeeId
     *
     * @return self
     */
    public function setEmployeeId($employeeId): ServiceInterface;

    /**
     * Employee ID getter.
     *
     * @return string|null
     */
    public function getEmployeeId();

    /**
     * Service ID setter.
     *
     * @param string $serviceId
     *
     * @return self
     */
    public function setServiceId($serviceId): ServiceInterface;

    /**
     * Service ID getter.
     *
     * @return string|null
     */
    public function getServiceId();

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
}