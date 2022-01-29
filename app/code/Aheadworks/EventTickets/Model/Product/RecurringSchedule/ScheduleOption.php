<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule;

use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class ScheduleOption
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
class ScheduleOption extends AbstractExtensibleObject implements ScheduleOptionInterface
{
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
}
