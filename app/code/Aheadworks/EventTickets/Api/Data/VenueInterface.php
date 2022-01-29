<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface VenueInterface
 * @api
 */
interface VenueInterface extends ExtensibleDataInterface, StorefrontLabelsEntityInterface
{
    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'venue';

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const STATUS = 'status';
    const NAME = 'name';
    const ADDRESS = 'address';
    const COORDINATES = 'coordinates';
    const IMAGE_PATH = 'image_path';
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
     * Get address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Set address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * Get coordinates
     *
     * @return string
     */
    public function getCoordinates();

    /**
     * Set coordinates
     *
     * @param string $coordinates
     * @return $this
     */
    public function setCoordinates($coordinates);

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
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\VenueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\VenueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\VenueExtensionInterface $extensionAttributes
    );
}
