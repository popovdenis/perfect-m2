<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for ticket type search results
 * @api
 */
interface TicketTypeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket type list
     *
     * @return \Aheadworks\EventTickets\Api\Data\TicketTypeInterface[]
     */
    public function getItems();

    /**
     * Set ticket type list
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketTypeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
