<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabels;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Api\SortOrder;

/**
 * Class Repository
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabels
 */
class Repository
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
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Save storefront labels
     *
     * @param StorefrontLabelsEntityInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function save($entity)
    {
        if (!(int)$entity->getId()) {
            return false;
        }

        $this->deleteByEntity($entity);
        $currentLabelsData = $this->getCurrentLabelsData($entity);
        $this->insertCurrentLabelsData($currentLabelsData);

        return true;
    }

    /**
     * Retrieve labels data for specified entity
     *
     * @param StorefrontLabelsEntityInterface $entity
     * @return array
     * @throws \Exception
     */
    public function get($entity)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('aw_et_label'))
            ->where('entity_id = :entity_id')
            ->where('entity_type = :entity_type')
            ->order('store_id ' . SortOrder::SORT_ASC);
        $labelsData = $connection->fetchAll(
            $select,
            [
                'entity_id' => $entity->getId(),
                'entity_type' => $entity->getStorefrontLabelsEntityType()
            ]
        );
        return $labelsData;
    }

    /**
     * Delete all existed labels data for specified entity id and type
     *
     * @param int $id
     * @param string $storefrontLabelEntityType
     * @return bool
     * @throws \Exception
     */
    public function delete($id, $storefrontLabelEntityType)
    {
        $this->getConnection()->delete(
            $this->getTableName(),
            [
                'entity_id = ?' => $id,
                'entity_type = ?' => $storefrontLabelEntityType
            ]
        );
        return true;
    }

    /**
     * Delete all existed labels data for specified entity
     *
     * @param StorefrontLabelsEntityInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function deleteByEntity($entity)
    {
        return $this->delete($entity->getId(), $entity->getStorefrontLabelsEntityType());
    }

    /**
     * Retrieve array of current labels data to insert
     *
     * @param StorefrontLabelsEntityInterface $entity
     * @return array
     */
    private function getCurrentLabelsData($entity)
    {
        $currentLabelsData = [];
        /** @var StorefrontLabelsInterface $labelsRecord */
        foreach ($entity->getLabels() as $labelsRecord) {
            $currentLabelsData[] = [
                'entity_id' => (int)$entity->getId(),
                'entity_type' => $entity->getStorefrontLabelsEntityType(),
                'store_id' => $labelsRecord->getStoreId(),
                'title' => $labelsRecord->getTitle(),
                'description' => $labelsRecord->getDescription(),
            ];
        }
        return $currentLabelsData;
    }

    /**
     * Insert current labels data
     *
     * @param array $labelsRecordsToInsert
     * @return $this
     * @throws \Exception
     */
    private function insertCurrentLabelsData($labelsRecordsToInsert)
    {
        if (!empty($labelsRecordsToInsert)) {
            $this->getConnection()->insertMultiple($this->getTableName(), $labelsRecordsToInsert);
        }
        return $this;
    }

    /**
     * Retrieve table name
     *
     * @return string
     * @throws \Exception
     */
    private function getTableName()
    {
        return $this->metadataPool->getMetadata(StorefrontLabelsInterface::class)->getEntityTable();
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(StorefrontLabelsInterface::class)->getEntityConnectionName()
        );
    }
}
