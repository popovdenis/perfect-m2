<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class TimeSlots
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator
 */
class TimeSlots extends AbstractValidator
{
    /**
     * @inheritDoc
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @throws \Exception
     */
    public function isValid($recurringSchedule)
    {
        $errors = [];
        $timeSlots = $recurringSchedule->getTimeSlots();

        if (empty($timeSlots)) {
            $errors[] = __('You need to set up at least one time slot.');
        } else {
            if (count($timeSlots) == 1 && !$this->isTimeSlotValid(reset($timeSlots))) {
                $errors[] = __('You have invalid or intersect time slot values.');
            } else {
                foreach ($timeSlots as $key1 => $timeSlot1) {
                    foreach ($timeSlots as $key2 => $timeSlot2) {
                        if ($key1 > $key2 && $this->haveInvalidOrIntersectValues($timeSlot1, $timeSlot2)) {
                            $errors[] = __('You have invalid or intersect time slot values.');
                        }
                    }
                }
            }
        }

        $this->_addMessages($errors);

        return empty($this->getMessages());
    }

    /**
     * Check if time slots have valid and not intersect values
     *
     * @param TimeSlotInterface $timeSlot1
     * @param TimeSlotInterface $timeSlot2
     * @return bool
     * @throws \Exception
     */
    private function haveInvalidOrIntersectValues($timeSlot1, $timeSlot2)
    {
        $result = true;

        if ($timeSlot1->getStartTime() && $timeSlot1->getEndTime()
            && $timeSlot2->getStartTime() && $timeSlot2->getEndTime()
        ) {
            $result = false;
            $startTime1 = new \DateTime($timeSlot1->getStartTime());
            $startTime2 = new \DateTime($timeSlot2->getStartTime());
            $endTime1 = new \DateTime($timeSlot1->getEndTime());
            $endTime2 = new \DateTime($timeSlot2->getEndTime());

            if (!$this->isTimeSlotValid($timeSlot1) || !$this->isTimeSlotValid($timeSlot2)) {
                $result = true;
            }

            if (($startTime1 >= $startTime2 && $startTime1 < $endTime2)
                || ($endTime1 > $startTime2 && $startTime1 <= $endTime2)
                || ($startTime2 >= $startTime1 && $startTime2 < $endTime1)
                || ($endTime2 > $startTime1 && $startTime2 <= $endTime1)
                || ($startTime1 == $startTime2 && $endTime1 == $endTime2)
            ) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Check is time slot valid
     *
     * @param TimeSlotInterface $timeSlot
     * @return bool
     * @throws \Exception
     */
    private function isTimeSlotValid($timeSlot)
    {
        $startTime = new \DateTime($timeSlot->getStartTime());
        $endTime = new \DateTime($timeSlot->getEndTime());

        return $timeSlot->getStartTime() && $timeSlot->getEndTime() && $startTime < $endTime;
    }
}
