<?php
namespace Aheadworks\EventTickets\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Ticket type CRUD interface
 * @api
 */
interface TicketTypeRepositoryInterface
{
    /**
     * Save ticket type
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketTypeInterface $ticketType
     * @return \Aheadworks\EventTickets\Api\Data\TicketTypeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\EventTickets\Api\Data\TicketTypeInterface $ticketType);

    /**
     * Retrieve ticket type by id with storefront labels for specified store view
     *
     * @param int $ticketTypeId
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\TicketTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($ticketTypeId, $storeId = null);

    /**
     * Retrieve ticket types matching the specified criteria with storefront labels for specified store view
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\EventTickets\Api\Data\TicketTypeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null);
}
