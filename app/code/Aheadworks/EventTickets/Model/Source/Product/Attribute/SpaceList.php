<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute;

use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Data\Collection;
use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Convert\DataObject as DataObjectConverter;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SpaceList
 *
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute
 */
class SpaceList extends Select
{
    /**
     * @var string
     */
    protected $idField = SpaceInterface::ID;

    /**
     * @var SpaceRepositoryInterface
     */
    private $spaceRepository;

    /**
     * @param MetadataPool $metadataPool
     * @param AttributeFactory $eavAttributeFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param DataObjectConverter $dataObjectConverter
     * @param SpaceRepositoryInterface $spaceRepository
     */
    public function __construct(
        MetadataPool $metadataPool,
        AttributeFactory $eavAttributeFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        DataObjectConverter $dataObjectConverter,
        SpaceRepositoryInterface $spaceRepository
    ) {
        parent::__construct(
            $metadataPool,
            $eavAttributeFactory,
            $searchCriteriaBuilder,
            $sortOrderBuilder,
            $dataObjectConverter
        );
        $this->spaceRepository = $spaceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => true,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aheadworks Event Tickets Space Id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->eavAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * {@inheritdoc}
     */
    public function addValueSortToCollection($collection, $dir = Collection::SORT_ORDER_DESC)
    {
        $linkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();

        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        $tableName = $attributeCode . '_t';
        $collection->getSelect()
            ->joinLeft(
                [$tableName => $attributeTable],
                "e.{$linkField}={$tableName}.{$linkField}" .
                " AND {$tableName}.attribute_id='{$attributeId}'" .
                " AND {$tableName}.store_id='0'",
                []
            );
        $collection->getSelect()->order($tableName . '.value ' . $dir);
        return $this;
    }

    /**
     * Retrieve spaces for generating options
     *
     * @param string|array|null $ids
     * @return SpaceInterface[]|array
     */
    protected function getOptionsArray($ids = null)
    {
        $spacesArray = [];
        try {
            $sortOrder = $this->sortOrderBuilder
                ->setField(SpaceInterface::ID)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(SpaceInterface::STATUS, Status::STATUS_ENABLED)
                ->addSortOrder($sortOrder);
            if (null !== $ids) {
                $searchCriteria->addFilter(SpaceInterface::ID, $ids, 'in');
            }
            $spacesArray = $this->spaceRepository->getList($searchCriteria->create())->getItems();
        } catch (LocalizedException $exception) {
        }

        return $spacesArray;
    }
}
