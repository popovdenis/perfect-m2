<?php
namespace Aheadworks\EventTickets\Model\Email\Template\TransportBuilder;

use Aheadworks\EventTickets\Model\Email\Template\TransportBuilderInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder as FrameworkTransportBuilder;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\Exception\InvalidArgumentException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;

/**
 * Class Version233
 *
 * @package Aheadworks\EventTickets\Model\Email\Template\TransportBuilder
 */
class Version233 extends FrameworkTransportBuilder implements TransportBuilderInterface
{
    /**
     * Param that used for storing all message data until it will be used
     *
     * @var array
     */
    protected $messageData = [];

    /**
     * @var array
     */
    protected $messageBodyParts = [];

    /**
     * @var EmailMessageInterfaceFactory
     */
    protected $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    protected $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    protected $mimePartInterfaceFactory;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->emailMessageInterfaceFactory = $this->objectManager->get(EmailMessageInterfaceFactory::class);
        $this->mimeMessageInterfaceFactory = $this->objectManager->get(MimeMessageInterfaceFactory::class);
        $this->mimePartInterfaceFactory = $this->objectManager->get(MimePartInterfaceFactory::class);
        $this->addressConverter = $this->objectManager->get(AddressConverter::class);
    }

    /**
     * {@inheritDoc}
     */
    public function addCc($address, $name = '')
    {
        $this->addAddressByType('cc', $address, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTo($address, $name = '')
    {
        $this->addAddressByType('to', $address, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addBcc($address)
    {
        $this->addAddressByType('bcc', $address);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setReplyTo($email, $name = null)
    {
        $this->addAddressByType('replyTo', $email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @throws MailException
     */
    public function setFrom($from)
    {
        return $this->setFromByScope($from, null);
    }

    /**
     * {@inheritDoc}
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->addAddressByType('from', $result['email'], $result['name']);

        return $this;
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
        $attachmentPart = $this->mimePartInterfaceFactory->create(
            [
                'content' => $body,
                'type' => $mimeType,
                'fileName' => $filename,
                'disposition' => $disposition,
                'encoding' => $encoding,
            ]
        );
        $this->messageBodyParts[] = $attachmentPart;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function reset()
    {
        $this->messageData = [];
        $this->messageBodyParts = [];

        return parent::reset();
    }

    /**
     * @inheritDoc
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $content = $template->processTemplate();
        $contentPart = $this->mimePartInterfaceFactory->create(['content' => $content]);

        if ($template->getType() != TemplateTypesInterface::TYPE_TEXT
            && $template->getType() != TemplateTypesInterface::TYPE_HTML
        ) {
            throw new LocalizedException(
                new Phrase('Unknown template type')
            );
        }

        $this->messageBodyParts[] = $contentPart;

        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $this->messageBodyParts]
        );

        $this->messageData['subject'] = html_entity_decode(
            (string)$template->getSubject(),
            ENT_QUOTES
        );
        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);

        return $this;
    }

    /**
     * Handles possible incoming types of email (string or array)
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function addAddressByType(string $addressType, $email, ?string $name = null): void
    {
        if (is_string($email)) {
            $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            return;
        }
        $convertedAddressArray = $this->addressConverter->convertMany($email);
        if (isset($this->messageData[$addressType])) {
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType],
                $convertedAddressArray
            );
        } else {
            $this->messageData[$addressType] = $convertedAddressArray;
        }
    }
}
