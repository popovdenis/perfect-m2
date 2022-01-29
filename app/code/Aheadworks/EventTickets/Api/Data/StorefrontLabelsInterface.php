<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Store labels interface
 * @api
 */
interface StorefrontLabelsInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const STORE_ID = 'store_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    /**#@-*/

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
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

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
     * @return \Aheadworks\EventTickets\Api\Data\StorefrontLabelsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\StorefrontLabelsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\StorefrontLabelsExtensionInterface $extensionAttributes
    );
}
