<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Option
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Option extends AbstractExtensibleObject implements TicketOptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
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
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
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
    public function getKey()
    {
        return $this->_get(self::KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        return $this->setData(self::KEY, $key);
    }
}
