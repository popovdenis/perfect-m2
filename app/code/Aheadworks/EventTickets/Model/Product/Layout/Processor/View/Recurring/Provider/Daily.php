<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\ProviderInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ScheduleOption;

/**
 * Class Daily
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider
 */
class Daily implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig($recurringSchedule)
    {
        return [
            'hiddenDays' => $this->getHiddenDays($recurringSchedule),
            'dateBrackets' => $this->getDateBrackets($recurringSchedule)
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
        /** @var ScheduleOptionInterface $disabledWeekDays */
        $disabledWeekDays = $recurringSchedule->getOptionByKey(ScheduleOption::DISABLED_WEEK_DAYS);
        if ($disabledWeekDays && is_array($disabledWeekDays->getValue())) {
            return array_map('intval', $disabledWeekDays->getValue());
        }

        return [];
    }

    /**
     * Get Start/End event date
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return array
     */
    private function getDateBrackets($recurringSchedule)
    {
        $startDate = $recurringSchedule->getOptionByKey(ScheduleOption::START_DATE);
        $endDate = $recurringSchedule->getOptionByKey(ScheduleOption::END_DATE);

        return [
            'startDate' => $startDate ? $startDate->getValue() : null,
            'endDate' => $endDate ? $endDate->getValue() : null
        ];
    }
}
