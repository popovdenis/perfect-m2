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
    const WORKER_ID = 'worker_id';

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
    public function setWorkerId($workerId): EventInterface;

    /**
     * @inheritdoc
     */
    public function getWorkerId();
}