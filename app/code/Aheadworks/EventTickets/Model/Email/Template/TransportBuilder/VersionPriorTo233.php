<?php
namespace Aheadworks\EventTickets\Model\Email\Template\TransportBuilder;

use Aheadworks\EventTickets\Model\Email\Template\TransportBuilderInterface;
use Aheadworks\EventTickets\Model\Email\Template\MessageFactory;
use Magento\Framework\Mail\Template\TransportBuilder as FrameworkTransportBuilder;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Exception\MailException;

/**
 * Class VersionPriorTo233
 *
 * @package Aheadworks\EventTickets\Model\Email\Template\TransportBuilder
 */
class VersionPriorTo233 extends FrameworkTransportBuilder implements TransportBuilderInterface
{
    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param MessageFactory $customMessageFactory
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        MessageFactory $customMessageFactory
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->message = $customMessageFactory->create();
    }

    /**
     * {@inheritDoc}
     */
    public function addAttachment(
        $body,
        $filename = null,
        $mimeType = \Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Zend_Mime::ENCODING_BASE64
    ) {
        $this->message->setBodyAttachment($body, $mimeType, $disposition, $encoding, $filename);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @throws MailException
     */
    public function setFrom($from)
    {
        if (method_exists($this, 'setFromByScope')) {
            return $this->setFromByScope($from, null);
        } else {
            return parent::setFrom($from);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();
        $this->message->setPartsToBody();

        return $this;
    }
}
