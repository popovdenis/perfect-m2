<?php
namespace Aheadworks\EventTickets\Model\Ticket\Email;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Email\EmailMetadataInterface;
use Aheadworks\EventTickets\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\EventTickets\Model\Source\Ticket\EmailVariables;
use Aheadworks\EventTickets\Model\Ticket;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\Composite as VariableProcessorComposite;

/**
 * Class Processor
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Email
 */
class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var VariableProcessorComposite
     */
    private $variableProcessorComposite;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorComposite $variableProcessorComposite
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorComposite $variableProcessorComposite
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
    }

    /**
     * Process
     *
     * @param TicketInterface[] $tickets
     * @return EmailMetadataInterface
     */
    public function process($tickets)
    {
        $ticket = reset($tickets);
        return $this->getMetadata($ticket, $tickets);
    }

    /**
     * Retrieve metadata
     *
     * @param TicketInterface $ticket
     * @param TicketInterface[] $attachmentTickets
     * @return EmailMetadataInterface
     */
    private function getMetadata($ticket, $attachmentTickets)
    {
        $attachments = [];
        /** @var TicketInterface|\Aheadworks\EventTickets\Model\Ticket $attachmentTicket */
        foreach ($attachmentTickets as $attachmentTicket) {
            $attachments[] = $attachmentTicket->getPdf();
        }

        $storeId = $ticket->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($ticket, $attachmentTickets))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($ticket))
            ->setRecipientEmail($this->getRecipientEmail($ticket))
            ->setAttachments($attachments);

        return $emailMetaData;
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    private function getTemplateId($storeId)
    {
        return $this->config->getNewTicketEmailTemplate($storeId);
    }

    /**
     * Retrieve recipient name
     *
     * @param TicketInterface|Ticket $ticket
     * @return string
     */
    private function getRecipientName($ticket)
    {
        return $ticket->getAttendeeName();
    }

    /**
     * Retrieve recipient email
     *
     * @param TicketInterface|Ticket $ticket
     * @return string
     */
    private function getRecipientEmail($ticket)
    {
        return $ticket->getAttendeeEmail();
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param TicketInterface $ticket
     * @return array
     */
    private function prepareTemplateVariables($ticket, $tickets)
    {
        $templateVariables = [
            EmailVariables::TICKET => $ticket,
            EmailVariables::TICKETS => $tickets,
            EmailVariables::STORE => $this->storeManager->getStore($ticket->getStoreId())
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}
