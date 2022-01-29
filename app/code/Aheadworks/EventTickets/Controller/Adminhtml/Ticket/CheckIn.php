<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action\Context;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\TicketActionManagementInterface;

/**
 * Class CheckIn
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Ticket
 */
class CheckIn extends AbstractAction
{
    /**
     * @var TicketActionManagementInterface
     */
    private $ticketActionService;

    /**
     * @param Context $context
     * @param TicketActionManagementInterface $ticketActionService
     */
    public function __construct(
        Context $context,
        TicketActionManagementInterface $ticketActionService
    ) {
        parent::__construct($context);
        $this->ticketActionService = $ticketActionService;
    }

    /**
     * {@inheritdoc}
     */
    protected function performAction($ticketNumber)
    {
        $processedTickets = $this->ticketActionService->doAction(
            TicketInterface::CHECK_IN_ACTION_NAME,
            [$ticketNumber]
        );
        return (count($processedTickets) > 0);
    }
}
