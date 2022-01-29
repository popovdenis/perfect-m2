<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;

/**
 * Interface ProviderInterface
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
interface ProviderInterface
{
    /**
     * Get config
     *
     * @param ProductRecurringScheduleInterface $recurringEvent
     * @return array
     */
    public function getConfig($recurringEvent);
}
