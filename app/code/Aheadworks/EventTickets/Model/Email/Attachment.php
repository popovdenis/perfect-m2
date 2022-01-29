<?php
namespace Aheadworks\EventTickets\Model\Email;

use Magento\Framework\DataObject;

/**
 * Class Attachment
 *
 * @package Aheadworks\EventTickets\Model\Email
 */
class Attachment extends DataObject implements AttachmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAttachment()
    {
        return $this->getData(self::ATTACHMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachment($attachment)
    {
        return $this->setData(self::ATTACHMENT, $attachment);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }
}
