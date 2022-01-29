<?php
namespace Aheadworks\EventTickets\Model\Service\Ticket;

use Aheadworks\EventTickets\Api\TicketManagementInterface;
use Aheadworks\EventTickets\Model\Ticket\Creator as TicketCreator;
use Aheadworks\EventTickets\Model\Ticket\Processor as TicketProcessor;
use Aheadworks\EventTickets\Api\TicketActionManagementInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;

/**
 * Class Service
 *
 * @package Aheadworks\EventTickets\Model\Service
 */
class Service implements TicketManagementInterface
{
    /**
     * @var TicketCreator
     */
    private $ticketCreator;

    /**
     * @var TicketProcessor
     */
    private $ticketProcessor;

    /**
     * @var TicketActionManagementInterface
     */
    private $ticketActionService;

    /**
     * @param TicketCreator $ticketCreator
     * @param TicketProcessor $ticketProcessor
     * @param TicketActionManagementInterface $ticketActionService
     */
    public function __construct(
        TicketCreator $ticketCreator,
        TicketProcessor $ticketProcessor,
        TicketActionManagementInterface $ticketActionService
    ) {
        $this->ticketCreator = $ticketCreator;
        $this->ticketProcessor = $ticketProcessor;
        $this->ticketActionService = $ticketActionService;
    }

    /**
     * {@inheritdoc}
     */
    public function processOrderSaving($order)
    {
        $this->ticketCreator->createByOrder($order);
        $this->activatePendingTicketsByOrder($order);
    }

    /**
     * {@inheritdoc}
     */
    public function activatePendingTicketsByOrder($order)
    {
        $pendingTicketsToActivate = $this->ticketProcessor->getPendingTicketsToActivateByOrder($order);
        $activatedTickets = $this->ticketActionService->doAction(
            TicketInterface::ACTIVATE_ACTION_NAME,
            $pendingTicketsToActivate
        );
        return $activatedTickets;
    }
}
