<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\ProviderInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ScheduleOption;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\WeekDays;

/**
 * Class Weekly
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider
 */
class Weekly implements ProviderInterface
{
    /**
     * @var WeekDays
     */
    private $weekDays;

    /**
     * @param WeekDays $weekDays
     */
    public function __construct(WeekDays $weekDays)
    {
        $this->weekDays = $weekDays;
    }

    /**
     * @inheritDoc
     */
    public function getConfig($recurringSchedule)
    {
        return [
            'hiddenDays' => $this->getHiddenDays($recurringSchedule),
            'weeksRepeatCount' => $this->getWeekRepeatCount($recurringSchedule),
            'dateBrackets' => [
                'startDate' => $this->getStartFrom($recurringSchedule)
            ]
        ];
    }

    /**
     * Get excluded days of week from event
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return array
     */
    private function getHiddenDays($recurringSchedule)
    {
        /** @var ScheduleOptionInterface $enabledWeekDays */
        $enabledWeekDays = $recurringSchedule->getOptionByKey(ScheduleOption::WEEK_DAYS);
        if ($enabledWeekDays && is_array($enabledWeekDays->getValue())) {
            return array_keys(array_diff($this->weekDays->getOptionValues(), $enabledWeekDays->getValue()));
        }

        return [];
    }

    /**
     * Get week repeat count
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return string
     */
    private function getWeekRepeatCount($recurringSchedule)
    {
        $weeksCount = $recurringSchedule->getOptionByKey(ScheduleOption::WEEKS_COUNT);

        return $weeksCount ? $weeksCount->getValue() : null;
    }

    /**
     * Get start from date
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return string
     */
    private function getStartFrom($recurringSchedule)
    {
        $startDate = $recurringSchedule->getOptionByKey(ScheduleOption::START_DATE);

        return $startDate ? $startDate->getValue() : null;
    }
}
