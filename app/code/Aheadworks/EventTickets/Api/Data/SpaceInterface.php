<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SpaceInterface
 * @api
 */
interface SpaceInterface extends ExtensibleDataInterface, StorefrontLabelsEntityInterface
{
    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'space';

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const STATUS = 'status';
    const NAME = 'name';
    const VENUE_ID = 'venue_id';
    const TICKETS_QTY = 'tickets_qty';
    const IMAGE_PATH = 'image_path';
    const SECTORS = 'sectors';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

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
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

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
     * Get tickets qty
     *
     * @return int
     */
    public function getTicketsQty();

    /**
     * Set tickets qty
     *
     * @param int $ticketsQty
     * @return $this
     */
    public function setTicketsQty($ticketsQty);

    /**
     * Get image path
     *
     * @return string
     */
    public function getImagePath();

    /**
     * Set image path
     *
     * @param string $imagePath
     * @return $this
     */
    public function setImagePath($imagePath);

    /**
     * Get array of sectors, assigned to the current space
     *
     * @return \Aheadworks\EventTickets\Api\Data\SectorInterface[]
     */
    public function getSectors();

    /**
     * Set array of sectors, assigned to the current space
     *
     * @param \Aheadworks\EventTickets\Api\Data\SectorInterface[] $sectors
     * @return $this
     */
    public function setSectors($sectors);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\SpaceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\SpaceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\SpaceExtensionInterface $extensionAttributes
    );
}
