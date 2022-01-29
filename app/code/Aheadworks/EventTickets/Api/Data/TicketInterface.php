<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TicketInterface
 * @api
 */
interface TicketInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for actions, performed over the tickets
     */
    const ACTIVATE_ACTION_NAME = 'activate';
    const CANCEL_ACTION_NAME = 'cancel';
    const CHECK_IN_ACTION_NAME = 'checkIn';
    const UNDO_CHECK_IN_ACTION_NAME = 'undoCheckIn';
    const SEND_EMAIL_ACTION_NAME = 'sendEmail';
    const DOWNLOAD_ACTION_NAME = 'download';
    /**#@-*/

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const PRODUCT_ID = 'product_id';
    const STORE_ID = 'store_id';
    const NUMBER = 'number';
    const STATUS = 'status';
    const EMAIL_SENT = 'email_sent';
    const TICKET_TYPE_ID = 'ticket_type_id';
    const VENUE_ID = 'venue_id';
    const SECTOR_ID = 'sector_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const ATTENDEE_NAME = 'attendee_name';
    const ATTENDEE_EMAIL = 'attendee_email';
    const BASE_PRICE = 'base_price';
    const BASE_ORIGINAL_PRICE = 'base_original_price';
    const SECTOR_STOREFRONT_TITLE = 'sector_storefront_title';
    const TICKET_TYPE_STOREFRONT_TITLE = 'ticket_type_storefront_title';
    const EVENT_TITLE = 'event_title';
    const EVENT_ADDRESS = 'event_address';
    const EVENT_DESCRIPTION = 'event_description';
    const EVENT_START_DATE = 'event_start_date';
    const EVENT_END_DATE = 'event_end_date';
    const EVENT_IMAGE = 'event_image';
    const OPTIONS = 'options';
    const RECURRING_TIME_SLOT_ID = 'recurring_time_slot_id';
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
     * Get order id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

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
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber();

    /**
     * Set number
     *
     * @param string $number
     * @return $this
     */
    public function setNumber($number);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get email sent
     *
     * @return int
     */
    public function getEmailSent();

    /**
     * Set email sent
     *
     * @param int $emailSent
     * @return $this
     */
    public function setEmailSent($emailSent);

    /**
     * Get ticket type id
     *
     * @return int
     */
    public function getTicketTypeId();

    /**
     * Set ticket type id
     *
     * @param int $ticketTypeId
     * @return $this
     */
    public function setTicketTypeId($ticketTypeId);

    /**
     * Get venue id
     *
     * @return int
     */
    public function getVenueId();

    /**
     * Set venue id
     *
     * @param int $venueId
     * @return $this
     */
    public function setVenueId($venueId);

    /**
     * Get sector id
     *
     * @return int
     */
    public function getSectorId();

    /**
     * Set sector id
     *
     * @param int $sectorId
     * @return $this
     */
    public function setSectorId($sectorId);

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int|null $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set customer name
     *
     * @param string|null $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param string|null $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get base price
     *
     * @return float
     */
    public function getBasePrice();

    /**
     * Set base price
     *
     * @param float $basePrice
     * @return $this
     */
    public function setBasePrice($basePrice);

    /**
     * Get base original price
     *
     * @return float
     */
    public function getBaseOriginalPrice();

    /**
     * Set base original price
     *
     * @param float $baseOriginalPrice
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice);

    /**
     * Get sector storefront title
     *
     * @return string
     */
    public function getSectorStorefrontTitle();

    /**
     * Set sector storefront title
     *
     * @param string $title
     * @return $this
     */
    public function setSectorStorefrontTitle($title);

    /**
     * Get ticket type storefront title
     *
     * @return string
     */
    public function getTicketTypeStorefrontTitle();

    /**
     * Set ticket type storefront title
     *
     * @param string $title
     * @return $this
     */
    public function setTicketTypeStorefrontTitle($title);

    /**
     * Get event title
     *
     * @return string
     */
    public function getEventTitle();

    /**
     * Set event title
     *
     * @param string $title
     * @return $this
     */
    public function setEventTitle($title);

    /**
     * Get event address
     *
     * @return string
     */
    public function getEventAddress();

    /**
     * Set event address
     *
     * @param string $address
     * @return $this
     */
    public function setEventAddress($address);

    /**
     * Get event description
     *
     * @return string
     */
    public function getEventDescription();

    /**
     * Set event description
     *
     * @param string $description
     * @return $this
     */
    public function setEventDescription($description);

    /**
     * Get event start date
     *
     * @return string
     */
    public function getEventStartDate();

    /**
     * Set event start date
     *
     * @param string $startDate
     * @return $this
     */
    public function setEventStartDate($startDate);

    /**
     * Get event end date
     *
     * @return string
     */
    public function getEventEndDate();

    /**
     * Set event end date
     *
     * @param string $endDate
     * @return $this
     */
    public function setEventEndDate($endDate);

    /**
     * Get event image
     *
     * @return string
     */
    public function getEventImage();

    /**
     * Set event image
     *
     * @param string $image
     * @return $this
     */
    public function setEventImage($image);

    /**
     * Get recurring time slot id
     *
     * @return int|null
     */
    public function getRecurringTimeSlotId();

    /**
     * Set recurring time slot id
     *
     * @param int|null $id
     * @return $this
     */
    public function setRecurringTimeSlotId($id);

    /**
     * Get options
     *
     * @return \Aheadworks\EventTickets\Api\Data\TicketOptionInterface[]|null
     */
    public function getOptions();

    /**
     * Set options
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketOptionInterface[] $options
     * @return $this
     */
    public function setOptions($options);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\TicketExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\TicketExtensionInterface $extensionAttributes
    );
}
