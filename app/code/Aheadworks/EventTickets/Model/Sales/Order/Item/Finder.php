<?php
namespace Aheadworks\EventTickets\Model\Sales\Order\Item;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class Finder
{
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Find sales order item of the corresponding ticket
     *
     * @param TicketInterface $ticket
     * @return OrderItemInterface|null
     */
    public function getByTicket(TicketInterface $ticket)
    {
        $orderItem = null;
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                OrderItemInterface::ORDER_ID,
                $ticket->getOrderId()
            )->addFilter(
                OrderItemInterface::PRODUCT_ID,
                $ticket->getProductId()
            )
            ->addFilter(
                'product_options',
                '%' . $ticket->getNumber() . '%',
                'like'
            )->create()
        ;

        $orderItemList = $this->orderItemRepository->getList($searchCriteria);
        if ($orderItemList->getTotalCount() > 0) {
            $orderItems = $orderItemList->getItems();
            $orderItem = reset($orderItems);
        }

        return $orderItem;
    }
}
