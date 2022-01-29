<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductPersonalOptionInterface
 * @api
 */
interface ProductPersonalOptionInterface extends ExtensibleDataInterface, StorefrontLabelsEntityInterface
{
    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'product_personal_option';

    /**#@+
     * Product select options group
     */
    const OPTION_GROUP_DEFAULT = 'default';
    const OPTION_GROUP_TEXT = 'text';
    const OPTION_GROUP_SELECT = 'select';
    const OPTION_GROUP_DATE = 'date';
    /**#@-*/

    /**#@+
     * Product field option types
     */
    const OPTION_TYPE_NAME = 'name';
    const OPTION_TYPE_EMAIL = 'email';
    const OPTION_TYPE_PHONE_NUMBER = 'phone_number';
    const OPTION_TYPE_FIELD = 'field';
    const OPTION_TYPE_DROPDOWN = 'dropdown';
    const OPTION_TYPE_DATE = 'date';
    /**#@-*/

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const UID = 'uid';
    const PRODUCT_ID = 'product_id';
    const TYPE = 'type';
    const SORT_ORDER = 'sort_order';
    const IS_REQUIRE = 'require';
    const IS_APPLY_TO_ALL_TICKET_TYPES = 'apply_to_all_ticket_types';
    const VALUES = 'values';
    /**#@-*/

    /**
     * Get option id
     *
     * @return int
     */
    public function getId();

    /**
     * Set option id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

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
     * Get option type
     *
     * @return string
     */
    public function getType();

    /**
     * Set option type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

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
     * Is require
     *
     * @return bool
     */
    public function isRequire();

    /**
     * Set is require
     *
     * @param bool $isRequired
     * @return $this
     */
    public function setIsRequire($isRequired);

    /**
     * Is apply to all ticket types
     *
     * @return bool
     */
    public function isApplyToAllTicketTypes();

    /**
     * Set is apply to all ticket types
     *
     * @param bool $isApplyToAllTicketTypes
     * @return $this
     */
    public function setIsApplyToAllTicketTypes($isApplyToAllTicketTypes);

    /**
     * Get values
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface[]|null
     */
    public function getValues();

    /**
     * Set values
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface[] $values
     * @return $this
     */
    public function setValues($values);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionExtensionInterface $extensionAttributes
    );
}
