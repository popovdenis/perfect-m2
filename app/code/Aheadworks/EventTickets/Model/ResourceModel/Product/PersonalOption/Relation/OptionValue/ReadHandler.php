<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOption\Relation\OptionValue;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Api\SortOrder;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOption\Relation\OptionValue
 */
class ReadHandler implements ExtensionInterface
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ProductPersonalOptionValueInterfaceFactory
     */
    private $productPersonalOptionValueFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param ProductPersonalOptionValueInterfaceFactory $productPersonalOptionValueFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        ProductPersonalOptionValueInterfaceFactory $productPersonalOptionValueFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        $this->productPersonalOptionValueFactory = $productPersonalOptionValueFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var ProductPersonalOptionInterface $entity */
        $entityId = (int)$entity->getId();
        if (!$entityId) {
            return $entity;
        }
        $storeId = isset($arguments['store_id']) ? $arguments['store_id'] : 0;
        $entity->setValues($this->getRelatedDataByOption($entityId, $storeId));

        return $entity;
    }

    /**
     * Retrieve option related data
     *
     * @param int $entityId
     * @param int $storeId
     * @return array
     * @throws \Exception
     */
    private function getRelatedDataByOption($entityId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTableName())
            ->where(ProductPersonalOptionValueInterface::OPTION_ID . ' = :option_id')
            ->order(ProductPersonalOptionValueInterface::SORT_ORDER . ' ' . SortOrder::SORT_ASC);
        $optionValueIds = $connection->fetchCol($select, [ProductPersonalOptionValueInterface::OPTION_ID => $entityId]);

        $optionValues = [];
        foreach ($optionValueIds as $optionValueId) {
            /** @var ProductPersonalOptionValueInterface $optionValue */
            $optionValue = $this->productPersonalOptionValueFactory->create();
            $this->entityManager->load($optionValue, $optionValueId, ['store_id' => $storeId]);
            $optionValues[] = $optionValue;
        }

        return $optionValues;
    }

    /**
     * Retrieve table name
     *
     * @return string
     * @throws \Exception
     */
    private function getTableName()
    {
        return $this->metadataPool->getMetadata(ProductPersonalOptionValueInterface::class)->getEntityTable();
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
            $this->metadataPool->getMetadata(ProductPersonalOptionValueInterface::class)->getEntityConnectionName()
        );
    }
}
