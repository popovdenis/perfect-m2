<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\ScheduleType;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter\ScheduleOptions as ScheduleOptionsConverter;

/**
 * Class ScheduleOptions
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator
 */
class ScheduleOptions extends AbstractValidator
{
    /**
     * @var array
     */
    private $requiredScheduleOptionsMap = [
        ScheduleType::DAILY => [
            ScheduleOptionInterface::START_DATE,
            ScheduleOptionInterface::END_DATE
        ],
        ScheduleType::WEEKLY => [
            ScheduleOptionInterface::WEEK_DAYS,
            ScheduleOptionInterface::WEEKS_COUNT,
            ScheduleOptionInterface::START_DATE
        ],
        ScheduleType::MONTHLY => [
            ScheduleOptionInterface::MONTH_DAYS
        ]
    ];

    /**
     * @var ScheduleOptionsConverter
     */
    private $converter;

    /**
     * @param ScheduleOptionsConverter $converter
     */
    public function __construct(ScheduleOptionsConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @inheritDoc
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @throws \Exception
     */
    public function isValid($recurringSchedule)
    {
        $errors = [];
        $scheduleType = $recurringSchedule->getType();
        $scheduleOptionsFlat = $this->converter->toFlatArray($recurringSchedule->getScheduleOptions());
        $scheduleOptionsKeys = array_keys($scheduleOptionsFlat);
        $validationMap = $this->requiredScheduleOptionsMap[$scheduleType];
        $missingOptions = array_diff($validationMap, $scheduleOptionsKeys);

        if (!empty($missingOptions)) {
            $missingOptionsStr = implode(', ', $missingOptions);
            $errors[] = __('Missing schedule options: %1', $missingOptionsStr);
        } else {
            if ($scheduleType == ScheduleType::DAILY) {
                $startDate = new \DateTime($scheduleOptionsFlat[ScheduleOptionInterface::START_DATE]);
                $endDate = new \DateTime($scheduleOptionsFlat[ScheduleOptionInterface::END_DATE]);

                if ($startDate >= $endDate) {
                    $errors[] = __('Recurring End Date should be greater than Start Date.');
                }
            }
            if ($scheduleType == ScheduleType::MONTHLY) {
                $monthDays = $scheduleOptionsFlat[ScheduleOptionInterface::MONTH_DAYS];
                $monthDaysPrepared = is_string($monthDays) ? explode(',', $monthDays) : $monthDays;
                $monthDaysPrepared = array_filter($monthDaysPrepared, function ($dayNumber) {
                    return (int)$dayNumber && $dayNumber <= 31;
                });

                if (!is_array($monthDaysPrepared) || empty($monthDaysPrepared)) {
                    $errors[] = __('Day(s) of Month field has not valid values.');
                }
            }
        }

        $this->_addMessages($errors);

        return empty($this->getMessages());
    }
}
