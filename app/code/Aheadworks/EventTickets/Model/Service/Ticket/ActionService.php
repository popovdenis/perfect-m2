<?php
namespace Aheadworks\EventTickets\Model\Service\Ticket;

use Aheadworks\EventTickets\Api\TicketActionManagementInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\ActionPool;
use Aheadworks\EventTickets\Model\Ticket\Action\TicketResolver;

/**
 * Class ActionService
 *
 * @package Aheadworks\EventTickets\Model\Service\Ticket
 */
class ActionService implements TicketActionManagementInterface
{
    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @var TicketResolver
     */
    private $ticketResolver;

    /**
     * @param TicketResolver $ticketResolver
     * @param ActionPool $actionPool
     */
    public function __construct(
        TicketResolver $ticketResolver,
        ActionPool $actionPool
    ) {
        $this->actionPool = $actionPool;
        $this->ticketResolver = $ticketResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function doAction($actionName, $ticketsArray, $additionalData = [])
    {
        $processedTickets = [];
        $actionInstance = $this->actionPool->getAction($actionName);
        if (isset($actionInstance)) {
            $resolvedTicketsArray = $this->ticketResolver->getResolvedTicketsArray($ticketsArray);
            $processedTickets = $actionInstance->execute($resolvedTicketsArray, $additionalData);
        }
        return $processedTickets;
    }
}
