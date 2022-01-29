<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class
 *
 * @package Aheadworks\EventTickets\Model
 */
class StorefrontLabels extends AbstractExtensibleObject implements StorefrontLabelsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
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
        \Aheadworks\EventTickets\Api\Data\StorefrontLabelsExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
