<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for venue search results
 * @api
 */
interface VenueSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get venue list
     *
     * @return \Aheadworks\EventTickets\Api\Data\VenueInterface[]
     */
    public function getItems();

    /**
     * Set venue list
     *
     * @param \Aheadworks\EventTickets\Api\Data\VenueInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
