<?php
namespace Aheadworks\EventTickets\Controller\Ticket\Management;

use Aheadworks\EventTickets\Api\Data\TicketInterface;

/**
 * Class CheckIn
 *
 * @package Aheadworks\EventTickets\Controller\Ticket\Management
 */
class CheckIn extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function performAction($ticketNumber)
    {
        $processedTickets = $this->ticketActionService->doAction(
            TicketInterface::CHECK_IN_ACTION_NAME,
            [$ticketNumber]
        );
        if (count($processedTickets) > 0) {
            $this->messageManager->addSuccessMessage(__('Ticket has been checked in.'));
        } else {
            $this->messageManager->addErrorMessage(__('Couldn\'t check in this ticket.'));
        }
    }
}
