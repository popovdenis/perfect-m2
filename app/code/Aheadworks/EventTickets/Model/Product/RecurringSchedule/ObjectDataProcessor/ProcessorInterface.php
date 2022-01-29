<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor
 */
interface ProcessorInterface
{
    /**
     * Process data before save
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return ProductRecurringScheduleInterface
     */
    public function beforeSave($recurringSchedule);

    /**
     * Process data after load
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return ProductRecurringScheduleInterface
     */
    public function afterLoad($recurringSchedule);
}
