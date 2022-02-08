<?php

namespace Perfect\Event\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Event\Api\Data\EventInterface;
use Perfect\Event\Api\EventRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class EventRepository
 *
 * @package Perfect\Event\Model
 */
class EventRepository implements EventRepositoryInterface
{
    /**
     * @var \Perfect\Event\Api\Data\EventInterfaceFactory
     */
    private $eventFactory;
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var \Perfect\Event\Model\ResourceModel\Event
     */
    private $resourceModel;
    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * EventRepository constructor.
     *
     * @param \Perfect\Event\Api\Data\EventInterfaceFactory                      $eventFactory
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory               $searchResultsFactory
     * @param \Perfect\Event\Model\ResourceModel\Event                           $resourceModel
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \Perfect\Event\Api\Data\EventInterfaceFactory $eventFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Perfect\Event\Model\ResourceModel\Event $resourceModel,
        CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->eventFactory = $eventFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $eventId): EventInterface
    {
        $event = $this->eventFactory->create();
        $this->resourceModel->load($event, $eventId);

        if (!$event->getId()) {
            throw new NoSuchEntityException(__('Event was not found'));
        }

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /* @var  \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /* @var \Perfect\Event\Model\ResourceModel\Event\Collection */
        $collection = $this->eventFactory->create()->getCollection();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EventInterface $event): void
    {
        $this->resourceModel->save($event);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EventInterface $event)
    {
        try {
            $this->resourceModel->delete($event);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the appointment: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }
}