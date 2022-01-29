<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Aheadworks\EventTickets\Api\Data\TicketInterface;

/**
 * Class DownloadAction
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
class DownloadAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute($ticketsArray, $additionalData = [])
    {
        $processedTickets = [];

        /** @var TicketInterface|\Aheadworks\EventTickets\Model\Ticket $ticket */
        foreach ($ticketsArray as $ticket) {
            $processedTicket = null;
            try {
                if ($ticket->getPdf(true)) {
                    $processedTicket = $ticket;
                }
            } catch (\Exception $e) {
            }
            if (!empty($processedTicket)) {
                $processedTickets[] = $processedTicket;
            }
        }
        return $processedTickets;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionName()
    {
        return TicketInterface::DOWNLOAD_ACTION_NAME;
    }
}
