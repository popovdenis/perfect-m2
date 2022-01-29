<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductPersonalOptionValueInterface
 * @api
 */
interface ProductPersonalOptionValueInterface extends ExtensibleDataInterface, StorefrontLabelsEntityInterface
{
    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'product_personal_option_value';

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const OPTION_ID = 'option_id';
    const SORT_ORDER = 'sort_order';
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
     * Get option id
     *
     * @return int
     */
    public function getOptionId();

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

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
     * @return \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueExtensionInterface $extensionAttributes
    );
}
