<?php
namespace Aheadworks\EventTickets\Model\Product\Sector;

use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Ticket
 *
 * @package Aheadworks\EventTickets\Model\Product\Sector
 */
class Ticket extends AbstractExtensibleObject implements ProductSectorTicketInterface
{
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
    public function getTypeId()
    {
        return $this->_get(self::TYPE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeId($typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEarlyBirdPrice()
    {
        return $this->_get(self::EARLY_BIRD_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEarlyBirdPrice($earlyBirdPrice)
    {
        return $this->setData(self::EARLY_BIRD_PRICE, $earlyBirdPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalPrice()
    {
        return $this->_get(self::FINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalPrice($finalPrice)
    {
        return $this->setData(self::FINAL_PRICE, $finalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastDaysPrice()
    {
        return $this->_get(self::LAST_DAYS_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastDaysPrice($lastDaysPrice)
    {
        return $this->setData(self::LAST_DAYS_PRICE, $lastDaysPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersonalOptionUids()
    {
        return $this->_get(self::PERSONAL_OPTION_UIDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPersonalOptionUids($personalOptionUids)
    {
        return $this->setData(self::PERSONAL_OPTION_UIDS, $personalOptionUids);
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
        \Aheadworks\EventTickets\Api\Data\ProductSectorTicketExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
