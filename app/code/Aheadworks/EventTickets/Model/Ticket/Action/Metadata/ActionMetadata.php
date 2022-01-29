<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action\Metadata;

use Magento\Framework\DataObject;

/**
 * Class ActionMetadata
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action\Metadata
 */
class ActionMetadata extends DataObject implements ActionMetadataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->getData(self::CLASS_NAME);
    }
}
