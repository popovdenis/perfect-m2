<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;
use Aheadworks\EventTickets\Model\Ticket\Notifier\Grouping as TicketGrouping;
use Aheadworks\EventTickets\Model\Ticket\Notifier as TicketNotifier;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Model\Source\Email\Status as EmailStatus;

/**
 * Class SendEmailAction
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
class SendEmailAction extends AbstractAction
{
    /**
     * @var TicketGrouping
     */
    private $ticketGrouping;

    /**
     * @var TicketNotifier
     */
    private $ticketNotifier;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TicketStatusResolver $ticketStatusResolver
     * @param TicketGrouping $ticketGrouping
     * @param TicketNotifier $ticketNotifier
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        TicketStatusResolver $ticketStatusResolver,
        TicketGrouping $ticketGrouping,
        TicketNotifier $ticketNotifier,
        TicketRepositoryInterface $ticketRepository
    ) {
        parent::__construct($ticketStatusResolver);
        $this->ticketGrouping = $ticketGrouping;
        $this->ticketNotifier = $ticketNotifier;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute($ticketsArray, $additionalData = [])
    {
        $processedTickets = [];
        $groupedTickets = $this->ticketGrouping->process($ticketsArray);
        /** @var TicketInterface[] $ticketGroup */
        foreach ($groupedTickets as $groupAlias => $ticketGroup) {
            $isEmailSent = $this->ticketNotifier->processActivatedTicketGroup($ticketGroup);
            $updatedTicketsFromCurrGroup = $this->getUpdatedTickets($ticketGroup, $isEmailSent);
            $processedTickets = array_merge($processedTickets, $updatedTicketsFromCurrGroup);
        }
        return $processedTickets;
    }

    /**
     * Update processed tickets with current email status
     *
     * @param TicketInterface[] $ticketGroup
     * @param bool $isEmailSent
     * @return TicketInterface[]
     */
    private function getUpdatedTickets($ticketGroup, $isEmailSent)
    {
        $emailSentStatus = $isEmailSent ? EmailStatus::SENT : EmailStatus::FAILED;
        $updatedTickets = [];
        foreach ($ticketGroup as $ticket) {
            $updatedTicket = null;
            try {
                $ticket->setEmailSent($emailSentStatus);
                $updatedTicket = $this->ticketRepository->save($ticket);
            } catch (LocalizedException $e) {
            }
            if (!empty($updatedTicket)) {
                $updatedTickets[] = $updatedTicket;
            }
        }
        return $updatedTickets;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionName()
    {
        return TicketInterface::SEND_EMAIL_ACTION_NAME;
    }
}
