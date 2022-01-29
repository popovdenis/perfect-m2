<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\ProviderInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ScheduleOption;

/**
 * Class Monthly
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider
 */
class Monthly implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig($recurringSchedule)
    {
        return [
            'monthDays' => $this->getMonthDays($recurringSchedule)
        ];
    }

    /**
     * Get available days of month for ticket selling
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return array|null
     */
    private function getMonthDays($recurringSchedule)
    {
        $monthDaysOption = $recurringSchedule->getOptionByKey(ScheduleOption::MONTH_DAYS);

        return $monthDaysOption ? $monthDaysOption->getValue() : null;
    }
}
