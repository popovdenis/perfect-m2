<?php
namespace Aheadworks\EventTickets\Model\Product\Option;

use Aheadworks\EventTickets\Api\Data\AttendeeInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Attendee
 *
 * @package Aheadworks\EventTickets\Model\Product\Option
 */
class Attendee extends AbstractExtensibleModel implements AttendeeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAttendeeId()
    {
        return $this->getData(self::ATTENDEE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttendeeId($attendeeId)
    {
        return $this->setData(self::ATTENDEE_ID, $attendeeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductOption()
    {
        return $this->getData(self::PRODUCT_OPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductOption($productOption)
    {
        return $this->setData(self::PRODUCT_OPTION, $productOption);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
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
        \Aheadworks\EventTickets\Api\Data\AttendeeExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
