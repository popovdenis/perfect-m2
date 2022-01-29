<?php
namespace Aheadworks\EventTickets\Controller\Ticket\Management;

use Aheadworks\EventTickets\Api\Data\TicketInterface;

/**
 * Class UndoCheckIn
 *
 * @package Aheadworks\EventTickets\Controller\Ticket\Management
 */
class UndoCheckIn extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function performAction($ticketNumber)
    {
        $processedTickets = $this->ticketActionService->doAction(
            TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
            [$ticketNumber]
        );
        if (count($processedTickets) > 0) {
            $this->messageManager->addSuccessMessage(__('Ticket status has been set to "Unused".'));
        } else {
            $this->messageManager->addSuccessMessage(__('Ticket status unable set to "Unused".'));
        }
    }
}
