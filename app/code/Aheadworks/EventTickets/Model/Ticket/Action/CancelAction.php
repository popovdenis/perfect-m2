<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CancelAction
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
class CancelAction extends AbstractAction
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TicketStatusResolver $ticketStatusResolver
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        TicketStatusResolver $ticketStatusResolver,
        TicketRepositoryInterface $ticketRepository
    ) {
        parent::__construct($ticketStatusResolver);
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute($ticketsArray, $additionalData = [])
    {
        $processedTickets = [];
        /** @var TicketInterface $ticket */
        foreach ($ticketsArray as $ticket) {
            $processedTicket = null;
            try {
                $ticket->setStatus(TicketStatus::CANCELED);
                $processedTicket = $this->ticketRepository->save($ticket);
            } catch (LocalizedException $e) {
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
        return TicketInterface::CANCEL_ACTION_NAME;
    }
}
