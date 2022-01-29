<?php
namespace Aheadworks\EventTickets\Api\Data;

/**
 * Interface ScheduleOptionInterface
 * @package Aheadworks\EventTickets\Api\Data
 */
interface ScheduleOptionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const KEY = 'key';
    const VALUE = 'value';
    /**#@-*/

    /**#@+
     * Allowed keys
     */
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';
    const DISABLED_WEEK_DAYS = 'disabled_week_days';
    const WEEK_DAYS = 'week_days';
    const WEEKS_COUNT = 'weeks_count';
    const MONTH_DAYS = 'month_days';
    /**#@-*/

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key);

    /**
     * Get value
     *
     * @return string|array
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
}
