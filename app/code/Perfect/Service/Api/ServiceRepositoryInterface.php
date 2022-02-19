<?php

namespace Perfect\Service\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Perfect\Service\Api\Data\ServiceInterface;

/**
 * Interface ServiceRepositoryInterface
 *
 * @package Perfect\Service\Api
 */
interface ServiceRepositoryInterface
{
    /**
     * Return ServiceInterface instance
     *
     * @param int $serviceId
     *
     * @return ServiceInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $serviceId): ServiceInterface;

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
     * @param ServiceInterface $service
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ServiceInterface $service): void;

    /**
     * Delete service.
     *
     * @param ServiceInterface $service
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ServiceInterface $service);

    /**
     * Delete services by IDs.
     *
     * @param array $ids
     *
     * @return boolean
     */
    public function deleteByIds(array $ids);
}