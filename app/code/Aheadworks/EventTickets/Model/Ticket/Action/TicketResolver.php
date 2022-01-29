<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class TicketResolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
class TicketResolver
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Retrieve tickets array based on the given tickets data
     *
     * @param array $ticketsDataArray
     * @return TicketInterface[]
     */
    public function getResolvedTicketsArray($ticketsDataArray)
    {
        $resolvedTickets = [];
        foreach ($ticketsDataArray as $ticketData) {
            $ticket = $this->getResolvedTicket($ticketData);
            if (isset($ticket)) {
                $resolvedTickets[] = $ticket;
            }
        }
        return $resolvedTickets;
    }

    /**
     * Resolve ticket by the given ticket data
     *
     * @param mixed $ticketData
     * @return TicketInterface|null
     */
    public function getResolvedTicket($ticketData)
    {
        $ticket = null;
        try {
            if ($ticketData instanceof TicketInterface) {
                $ticket = $ticketData;
            } else {
                try {
                    $ticket = $this->ticketRepository->get($ticketData);
                } catch (NoSuchEntityException $exception) {
                    $ticket = $this->ticketRepository->getById($ticketData);
                }
            }
        } catch (\Exception $exception) {
        }
        return $ticket;
    }
}
