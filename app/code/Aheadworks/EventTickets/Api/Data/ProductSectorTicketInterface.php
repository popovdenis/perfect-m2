<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductSectorTicketInterface
 * @api
 */
interface ProductSectorTicketInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const PRODUCT_SECTOR_ID = 'product_sector_id';
    const UID = 'uid';
    const TYPE_ID = 'type_id';
    const EARLY_BIRD_PRICE = 'early_bird_price';
    const PRICE = 'price';
    const LAST_DAYS_PRICE = 'last_days_price';
    const FINAL_PRICE = 'final_price';
    const POSITION = 'position';
    const PERSONAL_OPTION_UIDS = 'personal_option_uids';
    /**#@-*/

    /**#@+
     * Constants for aw_et_product_sector_tickets_options
     */
    const PRODUCT_SECTOR_TICKET_UID = 'product_sector_ticket_uid';
    const PRODUCT_OPTION_UID = 'product_option_uid';
    /**#@-*/

    /**
     * Get option uid
     *
     * @return string
     */
    public function getUid();

    /**
     * Set option uid
     *
     * @param string $uid
     * @return $this
     */
    public function setUid($uid);

    /**
     * Get ticket type id
     *
     * @return int
     */
    public function getTypeId();

    /**
     * Set ticket type id
     *
     * @param int $typeId
     * @return $this
     */
    public function setTypeId($typeId);

    /**
     * Get early bird price
     *
     * @return float|null
     */
    public function getEarlyBirdPrice();

    /**
     * Set early bird price
     *
     * @param float|null $earlyBirdPrice
     * @return $this
     */
    public function setEarlyBirdPrice($earlyBirdPrice);

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get last days price
     *
     * @return float|null
     */
    public function getLastDaysPrice();

    /**
     * Set last days price
     *
     * @param float|null $lastDaysPrice
     * @return $this
     */
    public function setLastDaysPrice($lastDaysPrice);

    /**
     * Get final price
     *
     * @return float|null
     */
    public function getFinalPrice();

    /**
     * Set final price
     *
     * @param float $finalPrice
     * @return $this
     */
    public function setFinalPrice($finalPrice);

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get personal option unique ids
     *
     * @return string[]
     */
    public function getPersonalOptionUids();

    /**
     * Set personal option unique ids
     *
     * @param string[] $personalOptionUids
     * @return $this
     */
    public function setPersonalOptionUids($personalOptionUids);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductSectorTicketExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductSectorTicketExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductSectorTicketExtensionInterface $extensionAttributes
    );
}
