<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action\Context;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\EventTickets\Api\TicketActionManagementInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;

/**
 * Class MassUndoCheckIn
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Ticket
 */
class MassUndoCheckIn extends AbstractMassAction
{
    /**
     * @var TicketActionManagementInterface
     */
    private $ticketActionService;

    /**
     * @param Context $context
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param Filter $filter
     * @param TicketActionManagementInterface $ticketActionService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TicketRepositoryInterface $ticketRepositoryInterface
     */
    public function __construct(
        Context $context,
        TicketCollectionFactory $ticketCollectionFactory,
        Filter $filter,
        TicketActionManagementInterface $ticketActionService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TicketRepositoryInterface $ticketRepositoryInterface
    ) {
        parent::__construct(
            $context,
            $ticketCollectionFactory,
            $filter,
            $searchCriteriaBuilder,
            $ticketRepositoryInterface
        );
        $this->ticketActionService = $ticketActionService;
    }

    /**
     * {@inheritdoc}
     */
    protected function performAction($ticketsArray)
    {
        $processedTickets = $this->ticketActionService->doAction(
            TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
            $ticketsArray
        );
        return (count($processedTickets));
    }
}
