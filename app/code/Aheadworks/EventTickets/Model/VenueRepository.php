<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\VenueSearchResultsInterface;
use Aheadworks\EventTickets\Api\Data\VenueSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Venue as VenueResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Venue\CollectionFactory as VenueCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\AbstractRepository as StorefrontLabelsEntityAbstractRepository;

/**
 * Class VenueRepository
 *
 * @package Aheadworks\EventTickets\Model
 */
class VenueRepository extends StorefrontLabelsEntityAbstractRepository implements VenueRepositoryInterface
{
    /**
     * @var VenueResourceModel
     */
    private $resource;

    /**
     * @var VenueInterfaceFactory
     */
    private $venueInterfaceFactory;

    /**
     * @var VenueSearchResultsInterfaceFactory
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
     * @var VenueCollectionFactory
     */
    private $venueCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param VenueInterfaceFactory $venueInterfaceFactory
     * @param VenueSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param VenueResourceModel $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param VenueCollectionFactory $venueCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        VenueInterfaceFactory $venueInterfaceFactory,
        VenueSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        VenueResourceModel $resource,
        CollectionProcessorInterface $collectionProcessor,
        VenueCollectionFactory $venueCollectionFactory,
        StoreManagerInterface $storeManager,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->venueInterfaceFactory = $venueInterfaceFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->resource = $resource;
        $this->collectionProcessor = $collectionProcessor;
        $this->venueCollectionFactory = $venueCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function save(VenueInterface $venue)
    {
        try {
            $this->resource->save($venue);
            $this->registry[$venue->getId()] = $venue;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $venue;
    }

    /**
     * {@inheritdoc}
     */
    public function get($venueId, $storeId = null)
    {
        if (!isset($this->registry[$venueId])) {
            /** @var VenueInterface $venue */
            $venue = $this->venueInterfaceFactory->create();
            $this->applyStoreIdToObject($this->resource, $storeId);
            $this->resource->load($venue, $venueId);
            if (!$venue->getId()) {
                throw NoSuchEntityException::singleField('venueId', $venueId);
            }
            $this->registry[$venueId] = $venue;
        }
        return $this->registry[$venueId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\EventTickets\Model\ResourceModel\Venue\Collection $collection */
        $collection = $this->venueCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, VenueInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $this->applyStoreIdToObject($collection, $storeId);

        /** @var VenueSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Venue $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Venue $model
     * @return VenueInterface
     */
    private function getDataObject($model)
    {
        /** @var VenueInterface $object */
        $object = $this->venueInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            VenueInterface::class
        );
        return $object;
    }
}
