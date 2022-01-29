<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
abstract class AbstractAction
{
    /**
     * @var TicketStatusResolver
     */
    protected $ticketStatusResolver;

    /**
     * @param TicketStatusResolver $ticketStatusResolver
     */
    public function __construct(
        TicketStatusResolver $ticketStatusResolver
    ) {
        $this->ticketStatusResolver = $ticketStatusResolver;
    }

    /**
     * Perform specific action for the specified tickets
     * Return array of processed tickets
     *
     * @param TicketInterface[] $ticketsArray
     * @param array $additionalData
     * @return TicketInterface[]
     */
    public function execute($ticketsArray, $additionalData = [])
    {
        $availableTickets = $this->getTicketsAvailableForCurrentAction($ticketsArray);
        $processedTickets = $this->doExecute($availableTickets, $additionalData);
        return $processedTickets;
    }

    /**
     * Action inner logic
     * Return array of processed tickets
     * @todo: add logging for all exceptions
     *
     * @param TicketInterface[] $ticketsArray
     * @param array $additionalData
     * @return TicketInterface[]
     */
    abstract protected function doExecute($ticketsArray, $additionalData = []);

    /**
     * Retrieve current action name
     *
     * @return string
     */
    abstract protected function getActionName();

    /**
     * Select from the specified tickets for which this action is available
     *
     * @param TicketInterface[] $ticketsArray
     * @return TicketInterface[]
     */
    protected function getTicketsAvailableForCurrentAction($ticketsArray)
    {
        $ticketsAvailableForAction = [];
        foreach ($ticketsArray as $ticket) {
            if ($this->ticketStatusResolver->isActionAllowedForTicket($this->getActionName(), $ticket)) {
                $ticketsAvailableForAction[] = $ticket;
            }
        }
        return $ticketsAvailableForAction;
    }
}
