<?php

namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for sector search results
 * @api
 */
interface SectorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get sector list
     *
     * @return \Aheadworks\EventTickets\Api\Data\SectorInterface[]
     */
    public function getItems();

    /**
     * Set sector list
     *
     * @param \Aheadworks\EventTickets\Api\Data\SectorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
