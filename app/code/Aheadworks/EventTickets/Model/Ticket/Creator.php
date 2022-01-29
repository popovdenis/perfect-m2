<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem as GenerateFromOrderItem;
use Magento\Sales\Model\Order;

/**
 * Class Creator
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Creator
{
    /**
     * @var GenerateFromOrderItem
     */
    private $generateFromOrderItem;

    /**
     * @param GenerateFromOrderItem $generateFromOrderItem
     */
    public function __construct(
        GenerateFromOrderItem $generateFromOrderItem
    ) {
        $this->generateFromOrderItem = $generateFromOrderItem;
    }

    /**
     * Create tickets by order
     *
     * @param Order $order
     * @return TicketInterface[]
     * @throws \Exception
     */
    public function createByOrder($order)
    {
        $createdTickets = [];
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() != EventTicket::TYPE_CODE) {
                continue;
            }
            $currentItemTickets = $this->generateFromOrderItem->generateTickets($item);
            if (!empty($currentItemTickets)) {
                $createdTickets = array_merge($createdTickets, $currentItemTickets);
            }
        }

        return $createdTickets;
    }
}
