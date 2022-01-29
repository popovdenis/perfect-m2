<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Ticket;

use Aheadworks\EventTickets\Controller\Adminhtml\Ticket\Index as TicketIndex;

/**
 * Class Index
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Ticket
 */
class Index extends TicketIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::tickets';
}
