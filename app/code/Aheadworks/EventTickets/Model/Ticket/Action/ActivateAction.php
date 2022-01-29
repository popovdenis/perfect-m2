<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ActivateAction
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
class ActivateAction extends AbstractAction
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var SendEmailAction
     */
    private $sendEmailAction;

    /**
     * @param TicketStatusResolver $ticketStatusResolver
     * @param TicketRepositoryInterface $ticketRepository
     * @param SendEmailAction $sendEmailAction
     */
    public function __construct(
        TicketStatusResolver $ticketStatusResolver,
        TicketRepositoryInterface $ticketRepository,
        SendEmailAction $sendEmailAction
    ) {
        parent::__construct($ticketStatusResolver);
        $this->ticketRepository = $ticketRepository;
        $this->sendEmailAction = $sendEmailAction;
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
                $ticket->setStatus(TicketStatus::UNUSED);
                $processedTicket = $this->ticketRepository->save($ticket);
            } catch (LocalizedException $e) {
            }
            if (!empty($processedTicket)) {
                $processedTickets[] = $processedTicket;
            }
        }
        $ticketsAfterSendEmailAction = $this->sendEmailAction->execute($processedTickets, $additionalData);
        return $ticketsAfterSendEmailAction;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionName()
    {
        return TicketInterface::ACTIVATE_ACTION_NAME;
    }
}
