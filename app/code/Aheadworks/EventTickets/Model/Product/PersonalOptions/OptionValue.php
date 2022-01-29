<?php
namespace Aheadworks\EventTickets\Model\Product\PersonalOptions;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class OptionValue
 *
 * @package Aheadworks\EventTickets\Model\Product\PersonalOptions
 */
class OptionValue extends AbstractExtensibleObject implements ProductPersonalOptionValueInterface
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
    public function getOptionId()
    {
        return $this->_get(self::OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
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
        \Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueExtensionInterface $extensionAttributes
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
