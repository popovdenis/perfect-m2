<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\RecurringSchedule;

use Aheadworks\EventTickets\Model\Product\RecurringSchedulePersistor;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\RecurringSchedule
 */
class ReadHandler implements ExtensionInterface
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
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getTypeId() !== EventTicket::TYPE_CODE) {
            return $entity;
        }

        $recurringSchedule = $this->recurringSchedulePersistor->getByProductId($entity->getId());
        $extension = $entity->getExtensionAttributes();

        $extension->setAwEtRecurringSchedule($recurringSchedule);
        $entity->setExtensionAttributes($extension);

        return $entity;
    }
}
