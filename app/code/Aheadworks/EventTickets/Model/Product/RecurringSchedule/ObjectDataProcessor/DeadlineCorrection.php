<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor;

use Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter\DeadlineCorrection as DeadlineCorrectionConverter;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class DeadlineCorrection
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor
 */
class DeadlineCorrection implements ProcessorInterface
{
    /**
     * @var DeadlineCorrectionConverter
     */
    private $deadlineCorrectionConverter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param DeadlineCorrectionConverter $deadlineCorrectionConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        DeadlineCorrectionConverter $deadlineCorrectionConverter,
        SerializerInterface $serializer
    ) {
        $this->deadlineCorrectionConverter = $deadlineCorrectionConverter;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($recurringSchedule)
    {
        $deadlineCorrection = $recurringSchedule->getSellingDeadlineCorrection();

        if ($deadlineCorrection instanceof DeadlineCorrectionInterface) {
            $deadlineCorrectionArray = $this->deadlineCorrectionConverter->dataModelToArray($deadlineCorrection);
            $recurringSchedule->setSellingDeadlineCorrection($this->serializer->serialize($deadlineCorrectionArray));
        }

        return $recurringSchedule;
    }

    /**
     * @inheritDoc
     */
    public function afterLoad($recurringSchedule)
    {
        if (is_string($recurringSchedule->getSellingDeadlineCorrection())
            && !empty($recurringSchedule->getSellingDeadlineCorrection())
        ) {
            $deadlineCorrectionArray = $this->serializer->unserialize(
                $recurringSchedule->getSellingDeadlineCorrection()
            );
            $deadlineCorrection = $this->deadlineCorrectionConverter->arrayToDataModel($deadlineCorrectionArray);
            $recurringSchedule->setSellingDeadlineCorrection($deadlineCorrection);
        }

        return $recurringSchedule;
    }
}
