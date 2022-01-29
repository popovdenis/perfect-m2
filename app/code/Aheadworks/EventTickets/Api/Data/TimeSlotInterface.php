<?php
namespace Aheadworks\EventTickets\Api\Data;

/**
 * Interface TimeSlotInterface
 * @package Aheadworks\EventTickets\Api\Data
 */
interface TimeSlotInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const SCHEDULE_ID = 'schedule_id';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get schedule id
     *
     * @return int
     */
    public function getScheduleId();

    /**
     * Set schedule id
     *
     * @param int $scheduleId
     * @return $this
     */
    public function setScheduleId($scheduleId);

    /**
     * Get start time
     *
     * @return string
     */
    public function getStartTime();

    /**
     * Set start time
     *
     * @param string $time
     * @return $this
     */
    public function setStartTime($time);

    /**
     * Get end time
     *
     * @return string
     */
    public function getEndTime();

    /**
     * Set end time
     *
     * @param string $time
     * @return $this
     */
    public function setEndTime($time);
}
