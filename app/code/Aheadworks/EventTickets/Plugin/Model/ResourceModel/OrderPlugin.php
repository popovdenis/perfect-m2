<?php
namespace Aheadworks\EventTickets\Plugin\Model\ResourceModel;

use Aheadworks\EventTickets\Api\TicketManagementInterface;

/**
 * Class OrderPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Model\ResourceModel
 */
class OrderPlugin
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @param TicketManagementInterface $ticketManagement
     */
    public function __construct(TicketManagementInterface $ticketManagement)
    {
        $this->ticketManagement = $ticketManagement;
    }

    /**
     * Save order
     *
     * @param \Magento\Sales\Model\ResourceModel\Order $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Model\Order $object
     * @return \Magento\Sales\Model\ResourceModel\Order
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave($subject, \Closure $proceed, $object)
    {
        $result = $proceed($object);
        $this->ticketManagement->processOrderSaving($object);

        return $result;
    }
}
