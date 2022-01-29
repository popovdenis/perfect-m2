<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface OptionInterface
 * @api
 */
interface OptionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Buy request constants
     */
    const BUY_REQUEST_AW_ET_SLOTS = 'aw_et_slots';
    const BUY_REQUEST_AW_ET_TICKETS = 'aw_et_tickets';
    const BUY_REQUEST_SECTOR_ID = 'sector_id';
    const BUY_REQUEST_TYPE_ID = 'type_id';
    const BUY_REQUEST_ATTENDEE = 'attendee';
    const BUY_REQUEST_ATTENDEE_OPTIONS = 'attendee_options';
    const BUY_REQUEST_QTY = 'qty';
    const BUY_REQUEST_CONFIGURED = 'configured';
    const BUY_REQUEST_PRODUCT_IS_CONFIGURE = 'product_is_configure';
    /**#@-*/

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AMOUNT = 'aw_et_amount';
    const SECTOR_ID = 'aw_et_sector_id';
    const TICKET_TYPE_ID = 'aw_et_ticket_type_id';
    const TICKET_NUMBERS = 'aw_et_ticket_numbers';
    const OPTION_ATTENDEE_IDS = 'aw_et_option_attendee_ids';
    const ATTENDEE_IDS = 'aw_et_attendee_ids';
    const ATTENDEES = 'aw_et_attendees';
    const RECURRING_START_DATE = 'aw_et_recurring_start_date';
    const RECURRING_END_DATE = 'aw_et_recurring_end_date';
    const RECURRING_TIME_SLOT_ID = 'aw_et_recurring_time_slot_id';
    /**#@-*/

    /**
     * Get amount
     *
     * @return float
     */
    public function getAwEtAmount();

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAwEtAmount($amount);

    /**
     * Get sector id
     *
     * @return int
     */
    public function getAwEtSectorId();

    /**
     * Set sector id
     *
     * @param int $sectorId
     * @return $this
     */
    public function setAwEtSectorId($sectorId);

    /**
     * Get ticket type id
     *
     * @return int
     */
    public function getAwEtTicketTypeId();

    /**
     * Set ticket type id
     *
     * @param int $ticketTypeId
     * @return $this
     */
    public function setAwEtTicketTypeId($ticketTypeId);

    /**
     * Get ticket numbers
     *
     * @return string[]
     */
    public function getAwEtTicketNumbers();

    /**
     * Set ticket numbers
     *
     * @param string[] $ticketNumbers
     * @return $this
     */
    public function setAwEtTicketNumbers($ticketNumbers);

    /**
     * Get option attendee ids
     *
     * @return string Format: optId1,optId2...
     */
    public function getAwEtOptionAttendeeIds();

    /**
     * Set option attendee ids
     *
     * @param string $optionAttendeeIds
     * @return $this
     */
    public function setAwEtOptionAttendeeIds($optionAttendeeIds);

    /**
     * Get attendee ids
     *
     * @return string Format: attendeeId1,attendeeId2...
     */
    public function getAwEtAttendeeIds();

    /**
     * Set attendee ids
     *
     * @param string $attendeeIds
     * @return $this
     */
    public function setAwEtAttendeeIds($attendeeIds);

    /**
     * Get attendees
     *
     * @return \Aheadworks\EventTickets\Api\Data\AttendeeInterface[]|null
     */
    public function getAwEtAttendees();

    /**
     * Set attendees
     *
     * @param \Aheadworks\EventTickets\Api\Data\AttendeeInterface[] $attendees
     * @return $this
     */
    public function setAwEtAttendees($attendees);

    /**
     * Get recurring start date
     *
     * @return string
     */
    public function getAwEtRecurringStartDate();

    /**
     * Set recurring start date
     *
     * @param string $startDate
     * @return $this
     */
    public function setAwEtRecurringStartDate($startDate);

    /**
     * Get recurring end date
     *
     * @return string
     */
    public function getAwEtRecurringEndDate();

    /**
     * Set recurring end date
     *
     * @param string $endDate
     * @return $this
     */
    public function setAwEtRecurringEndDate($endDate);

    /**
     * Get recurring time slot ID
     *
     * @return int
     */
    public function getAwEtRecurringTimeSlotId();

    /**
     * Set recurring time slot ID
     *
     * @param int $timeSlotId
     * @return $this
     */
    public function setAwEtRecurringTimeSlotId($timeSlotId);

    /**
     * Get sector id
     *
     * @return int
     */
    public function getSectorId();

    /**
     * Set sector id
     *
     * @param int $id
     * @return $this
     */
    public function setSectorId($id);

    /**
     * Get type id
     *
     * @return int
     */
    public function getTypeId();

    /**
     * Set type id
     *
     * @param int $id
     * @return $this
     */
    public function setTypeId($id);

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);


    /**
     * Get slots
     *
     * @return \Aheadworks\EventTickets\Api\Data\OptionInterface[]
     */
    public function getAwEtSlots();

    /**
     * Set slots
     *
     * @param \Aheadworks\EventTickets\Api\Data\OptionInterface[] $slots
     * @return $this
     */
    public function setAwEtSlots($slots);

    /**
     * Get tickets
     *
     * @return \Aheadworks\EventTickets\Api\Data\OptionInterface[]
     */
    public function getAwEtTickets();

    /**
     * Set tickets
     *
     * @param \Aheadworks\EventTickets\Api\Data\OptionInterface[] $tickets
     * @return $this
     */
    public function setAwEtTickets($tickets);

    /**
     * Get attendee options
     *
     * @return \Aheadworks\EventTickets\Api\Data\BuyRequest\AttendeeOptionInterface[]
     */
    public function getAttendeeOptions();

    /**
     * Set attendee options
     *
     * @param \Aheadworks\EventTickets\Api\Data\BuyRequest\AttendeeOptionInterface[]
     * @return $this
     */
    public function setAttendeeOptions($options);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\OptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\OptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\OptionExtensionInterface $extensionAttributes
    );
}
