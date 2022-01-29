<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Email\AttachmentInterface;
use Aheadworks\EventTickets\Model\Email\AttachmentInterfaceFactory;
use Aheadworks\EventTickets\Model\Ticket\Pdf\Creator as TicketPdfCreator;

/**
 * Class Processor
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Pdf
{
    /**
     * @var TicketPdfCreator
     */
    private $ticketPdfCreator;

    /**
     * @var AttachmentInterfaceFactory
     */
    private $attachmentInterfaceFactory;

    /**
     * @param TicketPdfCreator $ticketPdfCreator
     * @param AttachmentInterfaceFactory $attachmentInterfaceFactory
     */
    public function __construct(
        TicketPdfCreator $ticketPdfCreator,
        AttachmentInterfaceFactory $attachmentInterfaceFactory
    ) {
        $this->ticketPdfCreator = $ticketPdfCreator;
        $this->attachmentInterfaceFactory = $attachmentInterfaceFactory;
    }

    /**
     * Create ticket pdf view
     *
     * @param TicketInterface $ticket
     * @param bool $forDownload
     * @return AttachmentInterface
     */
    public function getTicketPdf($ticket, $forDownload = false)
    {
        /** @var AttachmentInterface $ticketPdf */
        $ticketPdf = $this->attachmentInterfaceFactory->create();
        $ticketPdf
            ->setAttachment($this->ticketPdfCreator->create($ticket, $forDownload))
            ->setFileName($this->generateFileName($ticket));

        return $ticketPdf;
    }

    /**
     * Generate file name from ticket data
     *
     * @param TicketInterface $ticket
     * @return string
     */
    private function generateFileName($ticket)
    {
        return $ticket->getNumber() . '.pdf';
    }
}
