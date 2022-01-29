<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor;

use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter\TimeSlots as TimeSlotsConverter;

/**
 * Class TimeSlots
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor
 */
class TimeSlots implements ProcessorInterface
{
    /**
     * @var TimeSlotsConverter
     */
    private $timeSlotsConverter;

    /**
     * @param TimeSlotsConverter $timeSlotsConverter
     */
    public function __construct(
        TimeSlotsConverter $timeSlotsConverter
    ) {
        $this->timeSlotsConverter = $timeSlotsConverter;
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($recurringSchedule)
    {
        if (is_array($recurringSchedule->getTimeSlots()) && !empty($recurringSchedule->getTimeSlots())) {
            $timeSlots = $recurringSchedule->getTimeSlots();
            $timeSlotsArray = $this->timeSlotsConverter->dataModelToArray($timeSlots);
            $recurringSchedule->setTimeSlots($timeSlotsArray);
        }

        return $recurringSchedule;
    }

    /**
     * @inheritDoc
     */
    public function afterLoad($recurringSchedule)
    {
        if (is_array($recurringSchedule->getTimeSlots()) && !empty($recurringSchedule->getTimeSlots())) {
            $timeSlotsArray = $recurringSchedule->getTimeSlots();
            $timeSlots = $this->timeSlotsConverter->arrayToDataModel($timeSlotsArray);
            $recurringSchedule->setTimeSlots($timeSlots);
        }

        return $recurringSchedule;
    }
}
