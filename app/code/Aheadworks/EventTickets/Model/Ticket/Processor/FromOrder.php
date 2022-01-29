<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Source\Ticket\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;

/**
 * Class FromOrder
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor
 */
class FromOrder
{
    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param OptionExtractor $optionExtractor
     * @param TicketRepositoryInterface $ticketRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OptionExtractor $optionExtractor,
        TicketRepositoryInterface $ticketRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->optionExtractor = $optionExtractor;
        $this->ticketRepository = $ticketRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve tickets to activate by order status
     *
     * @param Order $order
     * @return TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getTicketsToActivateByOrderStatus($order)
    {
        $toActivateTickets = $this->getPendingTicketsByOrder($order);

        return $toActivateTickets;
    }

    /**
     * Retrieve tickets to activate by order invoices
     *
     * @param Order $order
     * @return TicketInterface[]
     * @throws \Exception
     */
    public function getTicketsToActivateByOrderInvoices($order)
    {
        $toActivateTickets = [];
        /** @var Order\Invoice[] $invoices */
        $invoices = $order->getInvoiceCollection()->getItems();
        if (!empty($invoices)) {
            $toActivateTickets = $this->getTicketsToActivateByInvoices($order, $invoices);
        }

        return $toActivateTickets;
    }

    /**
     * Retrieve tickets to activate by invoices
     *
     * @param Order $order
     * @param Order\Invoice[] $invoices
     * @return TicketInterface[]|array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTicketsToActivateByInvoices($order, $invoices)
    {
        $toActivateTicketNum = [];
        $activeOrderTicketNum = $this->getActiveTicketNumbersByOrder($order);
        foreach ($invoices as $invoice) {
            if (!$invoice->wasPayCalled()) {
                continue;
            }
            /** @var Order\Invoice\Item $item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->getProductType() != EventTicket::TYPE_CODE) {
                    continue;
                }
                $options = $this->optionExtractor->extractFromArray($orderItem->getProductOptions());
                $createdTicketNum = $options->getAwEtTicketNumbers() ? : [];
                $activeItemTicketNum = array_intersect($createdTicketNum, $activeOrderTicketNum);
                $qtyToActivate = (int)$item->getQty();

                $availableTicketNum = $this->getAvailableToActivateTicketNum(
                    $qtyToActivate,
                    $createdTicketNum,
                    $activeItemTicketNum
                );
                $toActivateTicketNum = array_merge($toActivateTicketNum, $availableTicketNum);
            }
        }

        return !empty($toActivateTicketNum) ? $this->getPendingTicketsByOrder($order, $toActivateTicketNum) : [];
    }

    /**
     * Retrieve allow to activate ticket numbers
     *
     * @param int $qtyToActivate
     * @param array $createdTicketNum
     * @param array $activeItemTicketNum
     * @return array
     */
    private function getAvailableToActivateTicketNum($qtyToActivate, $createdTicketNum, $activeItemTicketNum)
    {
        $toActivateTicketNum = [];
        $availableQtyToActivate = count($createdTicketNum) - count($activeItemTicketNum);
        if ($availableQtyToActivate > 0) {
            $pendingTicketNum = array_diff($createdTicketNum, $activeItemTicketNum);
            $toActivateTicketNum = array_slice($pendingTicketNum, 0, $qtyToActivate);
        }

        return $toActivateTicketNum;
    }

    /**
     * Retrieve pending tickets by order
     *
     * @param OrderInterface $order
     * @param array $ticketNumbers
     * @return TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPendingTicketsByOrder($order, $ticketNumbers = [])
    {
        $this->searchCriteriaBuilder
            ->addFilter(TicketInterface::ORDER_ID, $order->getEntityId())
            ->addFilter(TicketInterface::STATUS, Status::PENDING);

        if (!empty($ticketNumbers)) {
            $this->searchCriteriaBuilder->addFilter(TicketInterface::NUMBER, $ticketNumbers, 'in');
        }
        $searchResults = $this->ticketRepository->getList($this->searchCriteriaBuilder->create());

        return $searchResults->getItems();
    }

    /**
     * Retrieve active ticket numbers by order
     *
     * @param OrderInterface $order
     * @return TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getActiveTicketNumbersByOrder($order)
    {
        $ticketNumbers = [];
        $tickets = $this->getActiveTicketByOrder($order);
        foreach ($tickets as $ticket) {
            $ticketNumbers[] = $ticket->getNumber();
        }

        return $ticketNumbers;
    }

    /**
     * Retrieve active tickets by order
     *
     * @param OrderInterface $order
     * @return TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getActiveTicketByOrder($order)
    {
        $this->searchCriteriaBuilder
            ->addFilter(TicketInterface::ORDER_ID, $order->getEntityId())
            ->addFilter(TicketInterface::STATUS, Status::PENDING, 'neq');

        $searchResults = $this->ticketRepository->getList($this->searchCriteriaBuilder->create());

        return $searchResults->getItems();
    }
}
