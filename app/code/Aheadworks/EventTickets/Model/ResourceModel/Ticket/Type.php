<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket;

use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractResourceModel
    as StorefrontLabelsEntityAbstractResourceModel;

/**
 * Class Type
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket
 */
class Type extends StorefrontLabelsEntityAbstractResourceModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_et_ticket_type', 'id');
    }
}
