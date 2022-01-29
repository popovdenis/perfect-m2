<?php
namespace Aheadworks\EventTickets\Model\Email\Template\Magento230;

use Magento\Framework\Mail\MailMessageInterface;
use Zend\Mime\Mime;
use Aheadworks\EventTickets\Model\Email\Template\Message as BaseMessage;

/**
 * Class Message
 * @package Aheadworks\EventTickets\Model\Email\Template\Magento230
 */
class Message extends BaseMessage implements MailMessageInterface
{
    /**
     * {@inheritDoc}
     */
    public function setBodyText($content)
    {
        $textPart = $this->partFactory->create();
        $textPart->setContent($content)
            ->setType(Mime::TYPE_TEXT)
            ->setCharset($this->zendMessage->getEncoding());
        $this->parts[] = $textPart;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setBodyHtml($content)
    {
        $htmlPart = $this->partFactory->create();
        $htmlPart->setContent($content)
            ->setType(Mime::TYPE_HTML)
            ->setCharset($this->zendMessage->getEncoding());
        $this->parts[] = $htmlPart;

        return $this;
    }

    /**
     * Set parts to Zend message body
     *
     * @return $this
     */
    public function setPartsToBody()
    {
        $mimeMessage = $this->mimeMessageFactory->create();
        $mimeMessage->setParts($this->parts);
        $this->zendMessage->setBody($mimeMessage);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFrom($fromAddress)
    {
        $this->zendMessage->setFrom($fromAddress);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setBody($body)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setMessageType($type)
    {
        return $this;
    }
}
