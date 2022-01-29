<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\RecurringSchedule\TimeSlot;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\RecurringSchedule;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\HandlerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class Handler
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\RecurringSchedule\TimeSlot
 */
class Handler implements HandlerInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(RecurringSchedule::TIME_SLOTS_TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     * @param ProductRecurringScheduleInterface $entity
     */
    public function save($entity)
    {
        $entityId = (int)$entity->getId();
        $timeSlots = $entity->getTimeSlots() ?: [];
        $connection = $this->resourceConnection->getConnection();
        $isDuplicate = $entity->getIsDuplicate();
        $existedTimeSlotIds = [];

        if (!empty($timeSlots)) {
            foreach ($timeSlots as &$timeSlot) {
                $timeSlot[TimeSlotInterface::SCHEDULE_ID] = $entityId;
                if ($isDuplicate){
                    unset($timeSlot[TimeSlotInterface::ID]);
                } else {
                    $id = $timeSlot[TimeSlotInterface::ID] ?? null;
                    if ($id) {
                        $existedTimeSlotIds[] = $id;
                    }
                }
            }
            $connection->delete(
                $this->tableName,
                [
                    TimeSlotInterface::ID . ' NOT IN (?)' => $existedTimeSlotIds,
                    TimeSlotInterface::SCHEDULE_ID. ' = ?' => $entityId
                ]
            );
            $connection->insertOnDuplicate($this->tableName, $timeSlots);
        }


        return $entity;
    }

    /**
     * {@inheritdoc}
     * @param ProductRecurringScheduleInterface $entity
     */
    public function load($entity)
    {
        $entityId = (int)$entity->getId();
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->tableName)
            ->where(TimeSlotInterface::SCHEDULE_ID . '= :id');
        $timeSlots = $connection->fetchAll($select, ['id' => $entityId]);

        $entity->setTimeSlots($timeSlots ?: []);

        return $entity;
    }
}
