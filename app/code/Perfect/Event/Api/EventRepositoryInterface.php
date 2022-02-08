<?php

namespace Perfect\Event\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Perfect\Event\Api\Data\EventInterface;

/**
 * Interface EventRepositoryInterface
 *
 * @package Perfect\Event\Api
 */
interface EventRepositoryInterface
{
    /**
     * Return EventInterface instance
     *
     * @param int $eventId
     *
     * @return EventInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $eventId): EventInterface;

    /**
     * Retrieve Events List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getEntities(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Save event
     *
     * @param EventInterface $event
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(EventInterface $event): void;

    /**
     * Delete event.
     *
     * @param EventInterface $event
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(EventInterface $event);
}