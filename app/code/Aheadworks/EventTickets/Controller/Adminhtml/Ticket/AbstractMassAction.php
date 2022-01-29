<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Collection as TicketCollection;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;

/**
 * Class AbstractMassAction
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Ticket
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::tickets';

    /**
     * @var TicketCollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * @param Context $context
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param Filter $filter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TicketRepositoryInterface $ticketRepositoryInterface
     */
    public function __construct(
        Context $context,
        TicketCollectionFactory $ticketCollectionFactory,
        Filter $filter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TicketRepositoryInterface $ticketRepositoryInterface
    ) {
        parent::__construct($context);
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->filter = $filter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            /** @var TicketInterface[] $ticketsArray */
            $ticketsArray = $this->getTicketsArray();
            $updatedTicketsCount = $this->performAction($ticketsArray);
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been updated', $updatedTicketsCount)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->getPreparedRedirect();
    }

    /**
     * Execute action inner logic
     *
     * @param TicketInterface[] $ticketsArray
     * @return int
     * @throws \Exception
     */
    abstract protected function performAction($ticketsArray);

    /**
     * Retrieve redirect to the tickets grid in the current state
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * Retrieve tickets collection
     *
     * @return TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTicketsArray()
    {
        /** @var TicketCollection $collection */
        $collection = $this->filter->getCollection($this->ticketCollectionFactory->create());
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(TicketInterface::ID, $collection->getAllIds(), 'in')
            ->create();

        return $this->ticketRepositoryInterface->getList($searchCriteria)->getItems();
    }
}
