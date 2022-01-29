<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule;

use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class TimeSlot
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
class TimeSlot extends AbstractExtensibleObject implements TimeSlotInterface
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
    public function getScheduleId()
    {
        return $this->_get(self::SCHEDULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduleId($scheduleId)
    {
        return $this->setData(self::SCHEDULE_ID, $scheduleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartTime()
    {
        return $this->_get(self::START_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartTime($time)
    {
        return $this->setData(self::START_TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndTime()
    {
        return $this->_get(self::END_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndTime($time)
    {
        return $this->setData(self::END_TIME, $time);
    }

    /**
     * Get data by key
     *
     * @param $key
     * @return mixed|null
     */
    public function getData($key)
    {
        return $this->_get($key);
    }
}
