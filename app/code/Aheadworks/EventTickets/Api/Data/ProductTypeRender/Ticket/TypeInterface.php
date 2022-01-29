<?php
namespace Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TypeInterface
 * @package Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket
 */
interface TypeInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const LABEL = 'label';
    const DESCRIPTION = 'description';
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
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeExtensionInterface $extAttr
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeExtensionInterface $extAttr
    );
}
