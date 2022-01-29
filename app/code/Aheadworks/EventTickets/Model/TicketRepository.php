<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketSearchResultsInterface;
use Aheadworks\EventTickets\Api\Data\TicketSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class TicketRepository
 *
 * @package Aheadworks\EventTickets\Model
 */
class TicketRepository implements TicketRepositoryInterface
{
    /**
     * @var TicketResourceModel
     */
    private $resource;

    /**
     * @var TicketInterfaceFactory
     */
    private $ticketInterfaceFactory;

    /**
     * @var TicketCollectionFactory
     */
    private $ticketCollectionFactory;

    /**
     * @var TicketSearchResultsInterfaceFactory
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
     * @var array
     */
    private $registryByNumber = [];

    /**
     * @param TicketResourceModel $resource
     * @param TicketInterfaceFactory $ticketInterfaceFactory
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param TicketSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        TicketResourceModel $resource,
        TicketInterfaceFactory $ticketInterfaceFactory,
        TicketCollectionFactory $ticketCollectionFactory,
        TicketSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->ticketInterfaceFactory = $ticketInterfaceFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TicketInterface $ticket)
    {
        try {
            $this->resource->save($ticket);
            $this->registry[$ticket->getId()] = $ticket;
            $this->registryByNumber[$ticket->getNumber()] = $ticket;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $ticket;
    }

    /**
     * {@inheritdoc}
     */
    public function get($number)
    {
        if (!isset($this->registryByNumber[$number])) {
            $ticketId = $this->resource->getIdByNumber($number);
            if (!$ticketId) {
                throw NoSuchEntityException::singleField('ticket number', $number);
            }
            /** @var TicketInterface $ticket */
            $ticket = $this->ticketInterfaceFactory->create();
            $this->resource->load($ticket, $ticketId);
            $this->registry[$ticketId] = $ticket;
            $this->registryByNumber[$number] = $ticket;
        }
        return $this->registryByNumber[$number];
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ticketId)
    {
        if (!isset($this->registry[$ticketId])) {
            /** @var TicketInterface $ticket */
            $ticket = $this->ticketInterfaceFactory->create();
            $this->resource->load($ticket, $ticketId);
            if (!$ticket->getId()) {
                throw NoSuchEntityException::singleField('ticketId', $ticketId);
            }
            $this->registry[$ticketId] = $ticket;
        }
        return $this->registry[$ticketId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Aheadworks\EventTickets\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->ticketCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TicketInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TicketSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Ticket $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Ticket $model
     * @return TicketInterface
     */
    private function getDataObject($model)
    {
        /** @var TicketInterface $object */
        $object = $this->ticketInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            TicketInterface::class
        );
        return $object;
    }
}
