<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AttendeeInterface
 * @api
 */
interface AttendeeInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ATTENDEE_ID = 'attendee_id';
    const PRODUCT_OPTION = 'product_option';
    const LABEL = 'label';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Get attendee id
     *
     * @return int
     */
    public function getAttendeeId();

    /**
     * Set attendee id
     *
     * @param int $attendeeId
     * @return $this
     */
    public function setAttendeeId($attendeeId);

    /**
     * Get product option
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface
     */
    public function getProductOption();

    /**
     * Set product option
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface $productOption
     * @return $this
     */
    public function setProductOption($productOption);

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\AttendeeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\AttendeeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\AttendeeExtensionInterface $extensionAttributes
    );
}
