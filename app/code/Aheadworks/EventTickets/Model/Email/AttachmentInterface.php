<?php
namespace Aheadworks\EventTickets\Model\Email;

/**
 * Interface AttachmentInterface
 *
 * @package Aheadworks\EventTickets\Model\Email
 */
interface AttachmentInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ATTACHMENT = 'attachment';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * Get attachment
     *
     * @return array|string
     */
    public function getAttachment();

    /**
     * Set attachment
     *
     * @param array|string $attachment
     * @return $this
     */
    public function setAttachment($attachment);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}
