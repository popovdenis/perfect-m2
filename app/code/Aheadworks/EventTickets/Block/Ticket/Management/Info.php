<?php
namespace Aheadworks\EventTickets\Block\Ticket\Management;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as StatusSource;
use Aheadworks\EventTickets\Model\Ticket;
use Aheadworks\EventTickets\Model\Url\ParamEncryptor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;

/**
 * Class Info
 *
 * @package Aheadworks\EventTickets\Block\Ticket\Management
 */
class Info extends Template
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ParamEncryptor
     */
    private $encryptor;

    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * @var TicketStatusResolver
     */
    private $ticketStatusResolver;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param ParamEncryptor $encryptor
     * @param StatusSource $statusSource
     * @param TicketStatusResolver $ticketStatusResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        OrderRepositoryInterface $orderRepository,
        ParamEncryptor $encryptor,
        StatusSource $statusSource,
        TicketStatusResolver $ticketStatusResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ticketRepository = $ticketRepository;
        $this->orderRepository = $orderRepository;
        $this->encryptor = $encryptor;
        $this->statusSource = $statusSource;
        $this->ticketStatusResolver = $ticketStatusResolver;
    }

    /**
     * Retrieve ticket info
     *
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface|Ticket|null
     */
    public function getTicketInfo()
    {
        $ticketNumber = trim($this->encryptor->decrypt('ticket_number', $this->getRequest()->getParam('key')));
        if (!empty($ticketNumber)) {
            try {
                return $this->ticketRepository->get($ticketNumber);
            } catch (NoSuchEntityException $e) {
                //do nothing
            }
        }

        return null;
    }

    /**
     * Retrieve order number by id
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderNumber($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            return $order->getIncrementId();
        } catch (NoSuchEntityException $e) {
            //do nothing
        }

        return '';
    }

    /**
     * Retrieve ticket status title
     *
     * @param $statusId
     * @return null|string
     */
    public function getTicketStatusTitle($statusId)
    {
        return $this->statusSource->getOptionLabelByValue($statusId);
    }

    /**
     * Check if allow undo check in
     *
     * @param TicketInterface $ticket
     * @return bool
     */
    public function isAllowUndoCheckIn($ticket)
    {
        return $this->ticketStatusResolver->isActionAllowedForTicket(
            TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
            $ticket
        );
    }
}
