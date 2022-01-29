<?php
namespace Aheadworks\EventTickets\Api\Data\BuyRequest;

/**
 * Interface AttendeeOptionsInterface
 * @package Aheadworks\EventTickets\Api\Data\BuyRequest
 */
interface AttendeeOptionInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const TICKET_NUMBER = 'ticket_number';
    const OPTION_ID = 'option_id';
    const OPTION_VALUE = 'option_value';
    /**#@-*/

    /**
     * Get ticket number
     *
     * @return int
     */
    public function getTicketNumber();

    /**
     * Set ticket number
     *
     * @param int $number
     * @return $this
     */
    public function setTicketNumber($number);

    /**
     * Get option id
     *
     * @return int
     */
    public function getOptionId();

    /**
     * Set option id
     *
     * @param int $id
     * @return $this
     */
    public function setOptionId($id);

    /**
     * Get option value
     *
     * @return string|int
     */
    public function getOptionValue();

    /**
     * Set option value
     *
     * @param string $value
     * @return $this
     */
    public function setOptionValue($value);
}
