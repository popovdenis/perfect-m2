<?php
namespace Aheadworks\EventTickets\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Venue CRUD interface
 * @api
 */
interface VenueRepositoryInterface
{
    /**
     * Save venue
     *
     * @param \Aheadworks\EventTickets\Api\Data\VenueInterface $venue
     * @return \Aheadworks\EventTickets\Api\Data\VenueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\EventTickets\Api\Data\VenueInterface $venue);

    /**
     * Retrieve venue by id with storefront labels for specified store view
     *
     * @param int $venueId
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\VenueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($venueId, $storeId = null);

    /**
     * Retrieve venues matching the specified criteria with storefront labels for specified store view
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\VenueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null);
}
