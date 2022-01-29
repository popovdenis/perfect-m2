<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\RecurringSchedule;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedulePersistor;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\RecurringSchedule
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var RecurringSchedulePersistor
     */
    private $recurringSchedulePersistor;

    /**
     * @param RecurringSchedulePersistor $recurringSchedulePersistor
     */
    public function __construct(
        RecurringSchedulePersistor $recurringSchedulePersistor
    ) {
        $this->recurringSchedulePersistor = $recurringSchedulePersistor;
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getTypeId() !== EventTicket::TYPE_CODE) {
            return $entity;
        }

        $entityId = $entity->getId();
        $scheduleType = $entity->getData(ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE);

        /** @var ProductRecurringScheduleInterface|null $recurringSchedule */
        $recurringSchedule = !empty($entity->getExtensionAttributes()->getAwEtRecurringSchedule())
            ? $entity->getExtensionAttributes()->getAwEtRecurringSchedule()
            : null;

        if ($recurringSchedule && $scheduleType == ScheduleType::RECURRING) {
            $recurringSchedule->setProductId($entityId);
            if ($entity->getIsDuplicate()) {
                $recurringSchedule
                    ->unsId()
                    ->setIsDuplicate(true);
            }

            $this->recurringSchedulePersistor->save($recurringSchedule);
        }

        return $entity;
    }
}
