<?php
namespace Aheadworks\EventTickets\Model\Email;

use Aheadworks\EventTickets\Model\Email\Template\TransportBuilderInterface;
use Aheadworks\EventTickets\Model\Email\Template\TransportBuilder\Factory as TransportBuilderFactory;

/**
 * Class Sender
 *
 * @package Aheadworks\EventTickets\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilderFactory
     */
    private $transportBuilderFactory;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory
    ) {
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    /**
     * Send email message
     *
     * @param EmailMetadataInterface $emailMetadata
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($emailMetadata)
    {
        /** @var TransportBuilderInterface $transportBuilder */
        $transportBuilder = $this->transportBuilderFactory->create();
        $transportBuilder
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFrom(['name' => $emailMetadata->getSenderName(), 'email' => $emailMetadata->getSenderEmail()])
            ->addTo($emailMetadata->getRecipientEmail(), $emailMetadata->getRecipientName());

        $attachments = $emailMetadata->getAttachments() ? : [];
        foreach ($attachments as $attachment) {
            $transportBuilder->addAttachment($attachment->getAttachment(), $attachment->getFileName());
        }
        $transportBuilder->getTransport()->sendMessage();

        return true;
    }
}
