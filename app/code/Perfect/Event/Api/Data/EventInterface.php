<?php

namespace Perfect\Event\Api\Data;

/**
 * Interface EventInterface
 *
 * @package Perfect\Event\Api\Data
 */
interface EventInterface
{
    /**
     * Constants defined for keys of array
     */
    const ID = 'id';
    const SERVICE_NAME = 'service_name';
    const DESCRIPTION = 'description';
    const STARTED_AT = 'started_at';
    const FINISHED_AT = 'finished_at';
    const ENABLED = 'enabled';
    const EMPLOYEE_ID = 'employee_id';
    const CLIENT_ID = 'client_id';
    const EVENT_COLOR = 'event_color';

    /**
     * Entity ID setter.
     *
     * @param string $entityId
     *
     * @return self
     */
    public function setId($entityId): EventInterface;

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
    public function setServiceName($serviceName): EventInterface;

    /**
     * Service name getter.
     *
     * @return string|null
     */
    public function getServiceName();

    /**
     * Service Description setter.
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description): EventInterface;

    /**
     * Description getter.
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Service started at setter.
     *
     * @param string $startedAt
     *
     * @return self
     */
    public function setStartedAt($startedAt): EventInterface;

    /**
     * Started at getter.
     *
     * @return string|null
     */
    public function getStartedAt();

    /**
     * Service finished at setter.
     *
     * @param string $finishedAt
     *
     * @return self
     */
    public function setFinishedAt($finishedAt): EventInterface;

    /**
     * Finished at getter.
     *
     * @return string|null
     */
    public function getFinishedAt();

    /**
     * Service enabled flag setter.
     *
     * @param string $flag
     *
     * @return self
     */
    public function setEnabled($flag): EventInterface;

    /**
     * Enabled getter.
     *
     * @return string|null
     */
    public function getEnabled();

    /**
     * Service employee id setter.
     *
     * @param string $employeeId
     *
     * @return self
     */
    public function setEmployeeId($employeeId): EventInterface;

    /**
     * Employee id getter.
     *
     * @return string|null
     */
    public function getEmployeeId();

    /**
     * Service client id setter.
     *
     * @param string $clientId
     *
     * @return self
     */
    public function setClientId($clientId): EventInterface;

    /**
     * Client id getter.
     *
     * @return string|null
     */
    public function getClientId();

    /**
     * Service event color setter.
     *
     * @param string $color
     *
     * @return self
     */
    public function setEventColor($color): EventInterface;

    /**
     * Event color getter.
     *
     * @return string|null
     */
    public function getEventColor();
}