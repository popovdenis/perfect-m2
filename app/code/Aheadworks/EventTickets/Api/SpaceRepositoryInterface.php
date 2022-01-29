<?php
namespace Aheadworks\EventTickets\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Space CRUD interface
 * @api
 */
interface SpaceRepositoryInterface
{
    /**
     * Save space
     *
     * @param \Aheadworks\EventTickets\Api\Data\SpaceInterface $space
     * @return \Aheadworks\EventTickets\Api\Data\SpaceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\EventTickets\Api\Data\SpaceInterface $space);

    /**
     * Retrieve space by id with storefront labels for specified store view
     *
     * @param int $spaceId
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\SpaceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($spaceId, $storeId = null);

    /**
     * Retrieve spaces matching the specified criteria with storefront labels for specified store view
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\SpaceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null);
}
