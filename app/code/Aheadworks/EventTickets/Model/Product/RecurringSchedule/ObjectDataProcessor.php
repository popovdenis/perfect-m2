<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor\ProcessorInterface;

/**
 * Class ObjectDataProcessor
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule
 */
class ObjectDataProcessor
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data before save
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return ProductRecurringScheduleInterface
     */
    public function prepareDataBeforeSave($recurringSchedule)
    {
        if (!$recurringSchedule->hasData('save_prepared')) {
            foreach ($this->processors as $processor) {
                if ($processor instanceof ProcessorInterface) {
                    $processor->beforeSave($recurringSchedule);
                }
            }
            $recurringSchedule->setData('save_prepared', true);
        }

        return $recurringSchedule;
    }

    /**
     * Prepare entity data after load
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return ProductRecurringScheduleInterface
     */
    public function prepareDataAfterLoad($recurringSchedule)
    {
        if (!$recurringSchedule->hasData('load_prepared')) {
            foreach ($this->processors as $processor) {
                if ($processor instanceof ProcessorInterface) {
                    $processor->afterLoad($recurringSchedule);
                }
            }
            $recurringSchedule->setData('load_prepared', true);
        }

        return $recurringSchedule;
    }
}
