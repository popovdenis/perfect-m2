<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SpaceSearchResultsInterface;
use Aheadworks\EventTickets\Api\Data\SpaceSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Space as SpaceResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Space\CollectionFactory as SpaceCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\AbstractRepository as StorefrontLabelsEntityAbstractRepository;

/**
 * Class SpaceRepository
 *
 * @package Aheadworks\EventTickets\Model
 */
class SpaceRepository extends StorefrontLabelsEntityAbstractRepository implements SpaceRepositoryInterface
{
    /**
     * @var SpaceResourceModel
     */
    private $resource;

    /**
     * @var SpaceInterfaceFactory
     */
    private $spaceInterfaceFactory;

    /**
     * @var SpaceSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SpaceCollectionFactory
     */
    private $spaceCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param SpaceInterfaceFactory $spaceInterfaceFactory
     * @param SpaceSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param SpaceResourceModel $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SpaceCollectionFactory $spaceCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        SpaceInterfaceFactory $spaceInterfaceFactory,
        SpaceSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        SpaceResourceModel $resource,
        CollectionProcessorInterface $collectionProcessor,
        SpaceCollectionFactory $spaceCollectionFactory,
        StoreManagerInterface $storeManager,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->spaceInterfaceFactory = $spaceInterfaceFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->resource = $resource;
        $this->collectionProcessor = $collectionProcessor;
        $this->spaceCollectionFactory = $spaceCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function save(SpaceInterface $space)
    {
        try {
            $this->resource->save($space);
            $this->registry[$space->getId()] = $space;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $space;
    }

    /**
     * {@inheritdoc}
     */
    public function get($spaceId, $storeId = null)
    {
        if (!isset($this->registry[$spaceId])) {
            /** @var SpaceInterface $space */
            $space = $this->spaceInterfaceFactory->create();
            $this->applyStoreIdToObject($this->resource, $storeId);
            $this->resource->load($space, $spaceId);
            if (!$space->getId()) {
                throw NoSuchEntityException::singleField('spaceId', $spaceId);
            }
            $this->registry[$spaceId] = $space;
        }
        return $this->registry[$spaceId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\EventTickets\Model\ResourceModel\Space\Collection $collection */
        $collection = $this->spaceCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, SpaceInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $this->applyStoreIdToObject($collection, $storeId);

        /** @var SpaceSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Space $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Space $model
     * @return SpaceInterface
     */
    private function getDataObject($model)
    {
        /** @var SpaceInterface $object */
        $object = $this->spaceInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            SpaceInterface::class
        );
        return $object;
    }
}
