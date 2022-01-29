<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabels\Repository as StorefrontLabelRepository;

/**
 * Class PersonalOptionRepository
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product
 */
class PersonalOptionRepository
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var StorefrontLabelRepository
     */
    private $storefrontLabelRepository;

    /**
     * @var ProductPersonalOptionInterfaceFactory
     */
    private $productPersonalOptionFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param StorefrontLabelRepository $storefrontLabelRepository
     * @param ProductPersonalOptionInterfaceFactory $productPersonalOptionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        StorefrontLabelRepository $storefrontLabelRepository,
        ProductPersonalOptionInterfaceFactory $productPersonalOptionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->storefrontLabelRepository = $storefrontLabelRepository;
        $this->productPersonalOptionFactory = $productPersonalOptionFactory;
    }

    /**
     * Save product option data
     *
     * @param ProductPersonalOptionInterface[] $options
     * @param int $entityId
     * @return bool
     * @throws \Exception
     */
    public function save($options, $entityId)
    {
        foreach ($options as $option) {
            try {
                $option->setId(null)->setProductId($entityId);
                $this->entityManager->save($option);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('Could not save personal options.'));
            }
        }

        return true;
    }

    /**
     * Retrieve product option data by product id
     *
     * @param int $productId
     * @param int $storeId
     * @return ProductPersonalOptionInterface[]
     * @throws \Exception
     */
    public function getByProductId($productId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getPersonalOptionTableName(), [ProductPersonalOptionInterface::ID])
            ->where(ProductPersonalOptionInterface::PRODUCT_ID . ' = :product_id')
            ->order(ProductPersonalOptionInterface::SORT_ORDER . ' ' . SortOrder::SORT_ASC);
        $productOptionIds = $connection->fetchCol($select, [ProductPersonalOptionInterface::PRODUCT_ID => $productId]);

        $productPersonalOptions = [];
        foreach ($productOptionIds as $productOptionId) {
            /** @var ProductPersonalOptionInterface $productPersonalOption */
            $productPersonalOption = $this->productPersonalOptionFactory->create();
            $this->entityManager->load($productPersonalOption, $productOptionId, ['store_id' => $storeId]);
            $productPersonalOptions[] = $productPersonalOption;
        }

        return $productPersonalOptions;
    }

    /**
     * Retrieve single product option by its uid
     *
     * @param string $uid
     * @param int $storeId
     * @return ProductPersonalOptionInterface
     * @throws \Exception
     */
    public function getByUid($uid, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getPersonalOptionTableName(), [ProductPersonalOptionInterface::ID])
            ->where(ProductPersonalOptionInterface::UID . ' = :uid');
        $productOptionId = $connection->fetchOne($select, [ProductPersonalOptionInterface::UID => $uid]);

        /** @var ProductPersonalOptionInterface $productPersonalOption */
        $productPersonalOption = $this->productPersonalOptionFactory->create();
        $this->entityManager->load($productPersonalOption, $productOptionId, ['store_id' => $storeId]);

        return $productPersonalOption;
    }

    /**
     * Delete all existed product options by product id
     *
     * @param int $productId
     * @return bool
     * @throws \Exception
     */
    public function deleteByProductId($productId)
    {
        $connection = $this->getConnection();
        $optionSelect = $connection->select()
            ->from($this->getPersonalOptionTableName(), [ProductPersonalOptionInterface::ID])
            ->where(ProductPersonalOptionInterface::PRODUCT_ID . ' = :product_id');
        $optionIds = $connection->fetchCol($optionSelect, [ProductPersonalOptionInterface::PRODUCT_ID => $productId]);

        $optionValueSelect = $connection->select()
            ->from($this->getPersonalOptionValueTableName(), [ProductPersonalOptionValueInterface::ID])
            ->where(ProductPersonalOptionValueInterface::OPTION_ID . ' IN (?)', $optionIds);
        $optionValIds = $connection->fetchCol($optionValueSelect);

        $this
            ->removeStorefrontLabels($optionValIds, ProductPersonalOptionValueInterface::STOREFRONT_LABELS_ENTITY_TYPE)
            ->removeStorefrontLabels($optionIds, ProductPersonalOptionInterface::STOREFRONT_LABELS_ENTITY_TYPE);

        $connection->delete(
            $this->getPersonalOptionTableName(),
            [ProductPersonalOptionInterface::PRODUCT_ID . ' = ?' => $productId]
        );
        return true;
    }

    /**
     * Remove all existed labels data for specified entity id and type
     *
     * @param int[] $entityIds
     * @return $this
     * @param string $storefrontLabelEntityType
     * @throws \Exception
     */
    private function removeStorefrontLabels($entityIds, $storefrontLabelEntityType)
    {
        foreach ($entityIds as $entityId) {
            $this->storefrontLabelRepository->delete($entityId, $storefrontLabelEntityType);
        }
        return $this;
    }

    /**
     * Retrieve personal option table name
     *
     * @return string
     * @throws \Exception
     */
    private function getPersonalOptionTableName()
    {
         return $this->metadataPool->getMetadata(ProductPersonalOptionInterface::class)->getEntityTable();
    }

    /**
     * Retrieve personal option value table name
     *
     * @return string
     * @throws \Exception
     */
    private function getPersonalOptionValueTableName()
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
            $this->metadataPool->getMetadata(ProductPersonalOptionInterface::class)->getEntityConnectionName()
        );
    }
}
