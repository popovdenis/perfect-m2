<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductRecurringScheduleInterface
 * @package Aheadworks\EventTickets\Api\Data
 */
interface ProductRecurringScheduleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const TYPE = 'type';
    const SELLING_DEADLINE_TYPE = 'selling_deadline_type';
    const SELLING_DEADLINE_CORRECTION = 'selling_deadline_correction';
    const SCHEDULE_OPTIONS = 'schedule_options';
    const TIME_SLOTS = 'time_slots';
    const DAYS_TO_DISPLAY = 'days_to_display';
    const FILTER_BY_TICKET_QTY = 'filter_by_ticket_qty';
    const MULTISELECTION_TIME_SLOTS = 'multiselection_time_slots';
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
     * Get product id
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get selling deadline type
     *
     * @return string
     */
    public function getSellingDeadlineType();

    /**
     * Set selling deadline type
     *
     * @param string $type
     * @return $this
     */
    public function setSellingDeadlineType($type);

    /**
     * Get selling deadline correction
     *
     * @return \Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface
     */
    public function getSellingDeadlineCorrection();

    /**
     * Set selling deadline correction
     *
     * @param \Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface $deadlineCorrection
     * @return $this
     */
    public function setSellingDeadlineCorrection($deadlineCorrection);

    /**
     * Get schedule options
     *
     * @return \Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface[]
     */
    public function getScheduleOptions();

    /**
     * Set schedule options
     *
     * @param \Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface[] $options
     * @return $this
     */
    public function setScheduleOptions($options);

    /**
     * Get time slots
     *
     * @return \Aheadworks\EventTickets\Api\Data\TimeSlotInterface[]
     */
    public function getTimeSlots();

    /**
     * Set time slots
     *
     * @param \Aheadworks\EventTickets\Api\Data\TimeSlotInterface[] $timeSlots
     * @return $this
     */
    public function setTimeSlots($timeSlots);

    /**
     * Get days to display
     *
     * @return int
     */
    public function getDaysToDisplay();

    /**
     * Set days to display
     *
     * @param int $daysToDisplay
     * @return $this
     */
    public function setDaysToDisplay($daysToDisplay);

    /**
     * Get filter by ticket qty
     *
     * @return int
     */
    public function getFilterByTicketQty();

    /**
     * Set filter by ticket qty
     *
     * @param int $filterByTicketQty
     * @return $this
     */
    public function setFilterByTicketQty($filterByTicketQty);

    /**
     * Get multiselection time slots
     *
     * @return int
     */
    public function getMultiselectionTimeSlots();

    /**
     * Set multiselection time slots
     *
     * @param int $multiselectionTimeSlots
     * @return $this
     */
    public function setMultiselectionTimeSlots($multiselectionTimeSlots);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleExtensionInterface $extensionAttributes
    );
}
