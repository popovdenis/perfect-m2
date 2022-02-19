<?php

namespace Perfect\EventService\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Perfect\EventService\Api\Data\EventServiceInterface;
use Perfect\EventService\Api\EventServiceRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class EventServiceRepository
 *
 * @package Perfect\EventService\Model
 */
class EventServiceRepository implements EventServiceRepositoryInterface
{
    /**
     * @var \Perfect\EventService\Api\Data\EventServiceInterface
     */
    private $serviceFactory;
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var \Perfect\EventService\Model\ResourceModel\EventService
     */
    private $resourceModel;
    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        \Perfect\EventService\Api\Data\EventServiceInterfaceFactory $serviceFactory,
        \Perfect\EventService\Model\ResourceModel\EventService $resourceModel,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->serviceFactory = $serviceFactory;
        $this->resourceModel = $resourceModel;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $serviceId): EventServiceInterface
    {
        $service = $this->serviceFactory->create();
        $this->resourceModel->load($service, $serviceId);

        if (!$service->getId()) {
            throw new NoSuchEntityException(__('Service was not found'));
        }

        return $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /* @var  \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /* @var \Perfect\EventService\Model\ResourceModel\EventService\Collection */
        $collection = $this->serviceFactory->create()->getCollection();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EventServiceInterface $service): void
    {
        $this->resourceModel->save($service);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EventServiceInterface $service)
    {
        try {
            $this->resourceModel->delete($service);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the service: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * @param array $ids
     *
     * @return bool|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByIds(array $ids)
    {
        try {
            $this->resourceModel->deleteByIds($ids);
        } catch (LocalizedException $exception) {
            throw new LocalizedException(__(
                'Could not delete services by ID(s): %1',
                $exception->getMessage()
            ));
        }
    }
}