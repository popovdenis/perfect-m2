<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Config;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Aheadworks\EventTickets\Model\Ticket\Processor\FromOrder as FromOrderProcessor;

/**
 * Class Processor
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var FromOrderProcessor
     */
    private $fromOrderProcessor;

    /**
     * @param Config $config
     * @param FromOrderProcessor $fromOrderProcessor
     */
    public function __construct(
        Config $config,
        FromOrderProcessor $fromOrderProcessor
    ) {
        $this->config = $config;
        $this->fromOrderProcessor = $fromOrderProcessor;
    }

    /**
     * Retrieve pending tickets to activate by order
     *
     * @param Order|OrderInterface $order
     * @return TicketInterface[]
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPendingTicketsToActivateByOrder($order)
    {
        if ($order->getStatus() == $this->config->getOrderStatusForTicketCreation()) {
            $toActivateTickets = $this->fromOrderProcessor->getTicketsToActivateByOrderStatus($order);
        } else {
            $toActivateTickets = $this->fromOrderProcessor->getTicketsToActivateByOrderInvoices($order);
        }

        return $toActivateTickets;
    }
}
