<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Email\Sender;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Aheadworks\EventTickets\Model\Ticket\Email\Processor as EmailProcessor;

/**
 * Class Notifier
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Notifier
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var EmailProcessor
     */
    private $emailProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Sender $sender
     * @param EmailProcessor $emailProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Sender $sender,
        EmailProcessor $emailProcessor,
        LoggerInterface $logger
    ) {
        $this->sender = $sender;
        $this->emailProcessor = $emailProcessor;
        $this->logger = $logger;
    }

    /**
     * Notify about ticket activated
     *
     * @param TicketInterface[] $ticketGroup
     * @return bool
     */
    public function processActivatedTicketGroup($ticketGroup)
    {
        $emailMetadata = $this->emailProcessor->process($ticketGroup);
        try {
            $this->sender->send($emailMetadata);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            return false;
        }
        return true;
    }
}
