<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for space search results
 * @api
 */
interface SpaceSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get space list
     *
     * @return \Aheadworks\EventTickets\Api\Data\SpaceInterface[]
     */
    public function getItems();

    /**
     * Set space list
     *
     * @param \Aheadworks\EventTickets\Api\Data\SpaceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
