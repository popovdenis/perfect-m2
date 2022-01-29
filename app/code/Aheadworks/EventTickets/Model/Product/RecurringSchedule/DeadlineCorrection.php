<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule;

use Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class DeadlineCorrection
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
class DeadlineCorrection extends AbstractExtensibleObject implements DeadlineCorrectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDays()
    {
        return $this->_get(self::DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDays($days)
    {
        return $this->setData(self::DAYS, $days);
    }

    /**
     * {@inheritdoc}
     */
    public function getHours()
    {
        return $this->_get(self::HOURS);
    }

    /**
     * {@inheritdoc}
     */
    public function setHours($hours)
    {
        return $this->setData(self::HOURS, $hours);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinutes()
    {
        return $this->_get(self::MINUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinutes($minutes)
    {
        return $this->setData(self::MINUTES, $minutes);
    }
}
