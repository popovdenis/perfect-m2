<?php

namespace Aheadworks\EventTickets\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Sector CRUD interface
 * @api
 */
interface SectorRepositoryInterface
{
    /**
     * Save sector
     *
     * @param \Aheadworks\EventTickets\Api\Data\SectorInterface $sector
     * @return \Aheadworks\EventTickets\Api\Data\SectorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\EventTickets\Api\Data\SectorInterface $sector);

    /**
     * Retrieve sector by id with storefront labels for specified store view
     *
     * @param int $sectorId
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\SectorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($sectorId, $storeId = null);

    /**
     * Retrieve sectors matching the specified criteria with storefront labels for specified store view
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\SectorSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null);
}
