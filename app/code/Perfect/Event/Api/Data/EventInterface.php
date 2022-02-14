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
    const APPOINTMENT_COLOR = 'appointment_color';

    /**
     * @inheritdoc
     */
    public function setId($entityId): EventInterface;

    /**
     * @inheritdoc
     */
    public function getId();

    /**
     * @inheritdoc
     */
    public function setServiceName($serviceName): EventInterface;

    /**
     * @inheritdoc
     */
    public function getServiceName();

    /**
     * @inheritdoc
     */
    public function setDescription($description): EventInterface;

    /**
     * @inheritdoc
     */
    public function getDescription();

    /**
     * @inheritdoc
     */
    public function setStartedAt($startedAt): EventInterface;

    /**
     * @inheritdoc
     */
    public function getStartedAt();

    /**
     * @inheritdoc
     */
    public function setFinishedAt($finishedAt): EventInterface;

    /**
     * @inheritdoc
     */
    public function getFinishedAt();

    /**
     * @inheritdoc
     */
    public function setEnabled($flag): EventInterface;

    /**
     * @inheritdoc
     */
    public function getEnabled();

    /**
     * @inheritdoc
     */
    public function setEmployeeId($employeeId): EventInterface;

    /**
     * @inheritdoc
     */
    public function getEmployeeId();

    /**
     * @inheritdoc
     */
    public function setClientId($clientId): EventInterface;

    /**
     * @inheritdoc
     */
    public function getClientId();

    /**
     * @inheritdoc
     */
    public function setAppointmentColor($color): EventInterface;

    /**
     * @inheritdoc
     */
    public function getAppointmentColor();
}