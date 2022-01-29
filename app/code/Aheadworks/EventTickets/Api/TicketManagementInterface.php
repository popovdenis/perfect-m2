<?php
namespace Aheadworks\EventTickets\Api;

/**
 * Interface TicketManagementInterface
 * @api
 */
interface TicketManagementInterface
{
    /**
     * Create pending tickets by order and activate tickets if needed
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function processOrderSaving($order);

    /**
     * Activate pending tickets by order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function activatePendingTicketsByOrder($order);
}
