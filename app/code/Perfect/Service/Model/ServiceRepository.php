<?php

namespace Perfect\Service\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Perfect\Service\Api\Data\ServiceInterface;
use Perfect\Service\Api\ServiceRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class ServiceRepository
 *
 * @package Perfect\Service\Model
 */
class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * @var \Perfect\Service\Api\Data\ServiceInterface
     */
    private $serviceFactory;
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var \Perfect\Service\Model\ResourceModel\Service
     */
    private $resourceModel;
    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        \Perfect\Service\Api\Data\ServiceInterfaceFactory $serviceFactory,
        \Perfect\Service\Model\ResourceModel\Service $resourceModel,
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
    public function get(int $serviceId): ServiceInterface
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
        /* @var \Perfect\Service\Model\ResourceModel\Service\Collection $collection */
        $collection = $this->serviceFactory->create()->getCollection();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    public function getServicesByMasterId(int $masterId)
    {
        /* @var \Perfect\Service\Model\ResourceModel\Service\Collection $collection */
        $collection = $this->serviceFactory->create()->getCollection();
        $collection->getSelect()
            ->joinLeft(
                ['se' => $collection->getTable('perfect_service_employee')],
                'se.service_id = main_table.entity_id AND '
                . $collection->getConnection()->quoteInto('se.employee_id = ?', $masterId),
                [
                    'master_service_duration_h' => 'service_duration_h',
                    'master_service_duration_m' => 'service_duration_m'
                ]
            )->joinLeft(
                ['sp' => $collection->getTable('perfect_service_price')],
                'sp.service_id = main_table.entity_id AND '
                . $collection->getConnection()->quoteInto('sp.employee_level = ?', 15),
                ['is_price_range', 'service_price_from', 'service_price_to']
            );

        return $collection->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function save(ServiceInterface $service): void
    {
        $this->resourceModel->save($service);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ServiceInterface $service)
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