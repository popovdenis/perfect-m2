<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketTypeSearchResultsInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type as TicketTypeResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\CollectionFactory as TicketTypeCollectionFactory;
use Aheadworks\EventTickets\Model\Ticket\Type;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\AbstractRepository as StorefrontLabelsEntityAbstractRepository;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TicketTypeRepository
 *
 * @package Aheadworks\EventTickets\Model
 */
class TicketTypeRepository extends StorefrontLabelsEntityAbstractRepository implements TicketTypeRepositoryInterface
{
    /**
     * @var TicketTypeResourceModel
     */
    private $resource;

    /**
     * @var TicketTypeInterfaceFactory
     */
    private $ticketTypeInterfaceFactory;

    /**
     * @var TicketTypeCollectionFactory
     */
    private $ticketTypeCollectionFactory;

    /**
     * @var TicketTypeSearchResultsInterfaceFactory
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param TicketTypeResourceModel $resource
     * @param TicketTypeInterfaceFactory $ticketTypeInterfaceFactory
     * @param TicketTypeCollectionFactory $ticketTypeCollectionFactory
     * @param TicketTypeSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param StoreManagerInterface $storeManager
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        TicketTypeResourceModel $resource,
        TicketTypeInterfaceFactory $ticketTypeInterfaceFactory,
        TicketTypeCollectionFactory $ticketTypeCollectionFactory,
        TicketTypeSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        StoreManagerInterface $storeManager,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->ticketTypeInterfaceFactory = $ticketTypeInterfaceFactory;
        $this->ticketTypeCollectionFactory = $ticketTypeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function save(TicketTypeInterface $ticketType)
    {
        try {
            $this->resource->save($ticketType);
            $this->registry[$ticketType->getId()] = $ticketType;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $ticketType;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ticketTypeId, $storeId = null)
    {
        if (!isset($this->registry[$ticketTypeId])) {
            /** @var TicketTypeInterface $ticketType */
            $ticketType = $this->ticketTypeInterfaceFactory->create();
            $this->applyStoreIdToObject($this->resource, $storeId);
            $this->resource->load($ticketType, $ticketTypeId);
            if (!$ticketType->getId()) {
                throw NoSuchEntityException::singleField('ticketTypeId', $ticketTypeId);
            }
            $this->registry[$ticketTypeId] = $ticketType;
        }
        return $this->registry[$ticketTypeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\Collection $collection */
        $collection = $this->ticketTypeCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TicketTypeInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $this->applyStoreIdToObject($collection, $storeId);

        /** @var TicketTypeSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Type $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Type $model
     * @return TicketTypeInterface
     */
    private function getDataObject($model)
    {
        /** @var TicketTypeInterface $object */
        $object = $this->ticketTypeInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            TicketTypeInterface::class
        );
        return $object;
    }
}
