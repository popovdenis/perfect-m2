<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor;

use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter\ScheduleOptions as ScheduleOptionsConverter;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ScheduleOptions
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor
 */
class ScheduleOptions implements ProcessorInterface
{
    /**
     * @var ScheduleOptionsConverter
     */
    private $scheduleOptionsConverter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ScheduleOptionsConverter $scheduleOptionsConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScheduleOptionsConverter $scheduleOptionsConverter,
        SerializerInterface $serializer
    ) {
        $this->scheduleOptionsConverter = $scheduleOptionsConverter;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($recurringSchedule)
    {
        if (is_array($recurringSchedule->getScheduleOptions())) {
            $scheduleOptions = $recurringSchedule->getScheduleOptions();
            $scheduleOptionsArray = $this->scheduleOptionsConverter->dataModelToArray($scheduleOptions);
            $recurringSchedule->setScheduleOptions($this->serializer->serialize($scheduleOptionsArray));
        }

        return $recurringSchedule;
    }

    /**
     * @inheritDoc
     */
    public function afterLoad($recurringSchedule)
    {
        if (is_string($recurringSchedule->getScheduleOptions()) && !empty($recurringSchedule->getScheduleOptions())) {
            $scheduleOptionsArray = $this->serializer->unserialize($recurringSchedule->getScheduleOptions());
            $scheduleOptions = $this->scheduleOptionsConverter->arrayToDataModel($scheduleOptionsArray);
            $recurringSchedule->setScheduleOptions($scheduleOptions);
        }

        return $recurringSchedule;
    }
}
