<?php
namespace Aheadworks\EventTickets\Model\Ticket\Status;

use Aheadworks\EventTickets\Api\Data\TicketInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Status
 */
class Resolver
{
    /**
     * @var RestrictionsPool
     */
    private $restrictionsPool;

    /**
     * @param RestrictionsPool $restrictionsPool
     */
    public function __construct(RestrictionsPool $restrictionsPool)
    {
        $this->restrictionsPool = $restrictionsPool;
    }

    /**
     * Check if specified action allowed to the pointed ticket
     *
     * @param string $actionName
     * @param TicketInterface $ticket
     * @return bool
     */
    public function isActionAllowedForTicket($actionName, $ticket)
    {
        return $this->isActionAllowedForTicketStatus($actionName, $ticket->getStatus());
    }

    /**
     * Check if specified action allowed to the ticket with pointed status
     *
     * @param string $actionName
     * @param int $ticketStatus
     * @return bool
     */
    public function isActionAllowedForTicketStatus($actionName, $ticketStatus)
    {
        $allowedActionsNames = [];
        try {
            $allowedActionsNames = $this->restrictionsPool->getRestrictions($ticketStatus)->getAllowedActionsNames();
        } catch (\Exception $exception) {
        }
        return in_array(
            $actionName,
            $allowedActionsNames
        );
    }
}
