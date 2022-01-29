<?php
namespace Aheadworks\EventTickets\Api\Data;

/**
 * Interface DeadlineCorrectionInterface
 * @package Aheadworks\EventTickets\Api\Data
 */
interface DeadlineCorrectionInterface
{
    /**#@+
     * Selling deadline correction keys
     */
    const DAYS = 'days';
    const HOURS = 'hours';
    const MINUTES = 'minutes';
    /**#@-*/

    /**
     * Get days
     *
     * @return int
     */
    public function getDays();

    /**
     * Set days
     *
     * @param int $days
     * @return $this
     */
    public function setDays($days);

    /**
     * Get hours
     *
     * @return int
     */
    public function getHours();

    /**
     * Set hours
     *
     * @param int $hours
     * @return $this
     */
    public function setHours($hours);

    /**
     * Get minutes
     *
     * @return int
     */
    public function getMinutes();

    /**
     * Set minutes
     *
     * @param int $minutes
     * @return $this
     */
    public function setMinutes($minutes);
}
