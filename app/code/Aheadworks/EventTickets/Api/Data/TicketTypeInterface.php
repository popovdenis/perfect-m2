<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TicketTypeInterface
 * @api
 */
interface TicketTypeInterface extends ExtensibleDataInterface, StorefrontLabelsEntityInterface
{
    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'ticket_type';

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const SKU = 'sku';
    const STATUS = 'status';
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
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\TicketTypeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketTypeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\TicketTypeExtensionInterface $extensionAttributes
    );
}
