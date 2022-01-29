<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SectorInterface
 * @api
 */
interface SectorInterface extends ExtensibleDataInterface, StorefrontLabelsEntityInterface
{
    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'sector';

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const STATUS = 'status';
    const NAME = 'name';
    const SKU = 'sku';
    const TICKETS_QTY = 'tickets_qty';
    const IMAGE_PATH = 'image_path';
    const SPACE_ID = 'space_id';
    const SORT_ORDER = 'sort_order';
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
     * Get sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

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
     * Get space id
     *
     * @return int
     */
    public function getSpaceId();

    /**
     * Set space id
     *
     * @param int $spaceId
     * @return $this
     */
    public function setSpaceId($spaceId);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\SectorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\SectorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\SectorExtensionInterface $extensionAttributes
    );
}
