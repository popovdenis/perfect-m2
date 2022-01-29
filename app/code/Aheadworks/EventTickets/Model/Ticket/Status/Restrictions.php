<?php
namespace Aheadworks\EventTickets\Model\Ticket\Status;

use Magento\Framework\DataObject;

/**
 * Class Restrictions
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Status
 */
class Restrictions extends DataObject implements RestrictionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllowedActionsNames()
    {
        return $this->getData(self::ALLOWED_ACTIONS_NAMES);
    }
}
