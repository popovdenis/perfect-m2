<?php
namespace Aheadworks\EventTickets\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Ticket CRUD interface
 * @api
 */
interface TicketRepositoryInterface
{
    /**
     * Save ticket
     *
     * @param \Aheadworks\EventTickets\Api\Data\TicketInterface $ticket
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\EventTickets\Api\Data\TicketInterface $ticket);

    /**
     * Retrieve ticket by number
     *
     * @param string $number
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($number);

    /**
     * Retrieve ticket by id
     *
     * @param int $ticketId
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($ticketId);

    /**
     * Retrieve tickets matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\EventTickets\Api\Data\TicketSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
