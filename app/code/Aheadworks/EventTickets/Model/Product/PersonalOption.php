<?php
namespace Aheadworks\EventTickets\Model\Product;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class PersonalOption
 *
 * @package Aheadworks\EventTickets\Model\Product
 */
class PersonalOption extends AbstractExtensibleObject implements ProductPersonalOptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->_get(self::UID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUid($uid)
    {
        return $this->setData(self::UID, $uid);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function isRequire()
    {
        return $this->_get(self::IS_REQUIRE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequire($isRequired)
    {
        return $this->setData(self::IS_REQUIRE, $isRequired);
    }

    /**
     * {@inheritdoc}
     */
    public function isApplyToAllTicketTypes()
    {
        return $this->_get(self::IS_APPLY_TO_ALL_TICKET_TYPES);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsApplyToAllTicketTypes($isApplyToAllTicketTypes)
    {
        return $this->setData(self::IS_APPLY_TO_ALL_TICKET_TYPES, $isApplyToAllTicketTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->_get(self::VALUES);
    }

    /**
     * {@inheritdoc}
     */
    public function setValues($values)
    {
        return $this->setData(self::VALUES, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        return $this->_get(self::LABELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabels($labelsRecordsArray)
    {
        return $this->setData(self::LABELS, $labelsRecordsArray);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLabels()
    {
        return $this->_get(self::CURRENT_LABELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLabels($labelsRecord)
    {
        return $this->setData(self::CURRENT_LABELS, $labelsRecord);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorefrontLabelsEntityType()
    {
        return self::STOREFRONT_LABELS_ENTITY_TYPE;
    }
}
