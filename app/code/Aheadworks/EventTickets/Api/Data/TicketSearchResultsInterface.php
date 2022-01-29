<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for ticket search results
 * @api
 */
interface TicketSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket list
     *
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface[]
     */
    public function getItems();

    /**
     * Set ticket list
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
