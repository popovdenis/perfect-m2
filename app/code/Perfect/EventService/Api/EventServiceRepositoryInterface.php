<?php

namespace Perfect\EventService\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Perfect\EventService\Api\Data\EventServiceInterface;

/**
 * Interface EventServiceRepositoryInterface
 *
 * @package Perfect\EventService\Api
 */
interface EventServiceRepositoryInterface
{
    /**
     * Return EventServiceInterface instance
     *
     * @param int $serviceId
     *
     * @return EventServiceInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $serviceId): EventServiceInterface;

    /**
     * Retrieve Services List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getEntities(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Save service.
     *
     * @param EventServiceInterface $service
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(EventServiceInterface $service): void;

    /**
     * Delete service.
     *
     * @param EventServiceInterface $service
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(EventServiceInterface $service);

    /**
     * Delete services by IDs.
     *
     * @param array $ids
     *
     * @return boolean
     */
    public function deleteByIds(array $ids);
}