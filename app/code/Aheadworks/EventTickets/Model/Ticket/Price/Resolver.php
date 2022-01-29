<?php
namespace Aheadworks\EventTickets\Model\Ticket\Price;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Sales\Order\Item\Finder as SalesOrderItemFinder;

class Resolver
{
    /**
     * @var SalesOrderItemFinder
     */
    private $salesOrderItemFinder;

    /**
     * @param SalesOrderItemFinder $salesOrderItemFinder
     */
    public function __construct(
        SalesOrderItemFinder $salesOrderItemFinder
    ) {
        $this->salesOrderItemFinder = $salesOrderItemFinder;
    }

    /**
     * Retrieve ticket price to show
     *
     * @param TicketInterface $ticket
     * @return float
     */
    public function getPriceToShow(TicketInterface $ticket)
    {
        $priceToShow = $ticket->getBasePrice();
        if ($ticket->getBaseOriginalPrice() > 0) {
            $priceToShow = $ticket->getBaseOriginalPrice();
        } else {
            $salesOrderItem = $this->salesOrderItemFinder->getByTicket($ticket);
            if ($salesOrderItem) {
                $priceToShow = $salesOrderItem->getBaseOriginalPrice();
            }
        }

        return $priceToShow;
    }
}
