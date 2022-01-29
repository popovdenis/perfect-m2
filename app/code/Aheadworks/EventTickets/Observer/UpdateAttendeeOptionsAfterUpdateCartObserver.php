<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Model\Product\Quote\Item\AttendeeOptionProcessor;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Class UpdateAttendeeOptionsAfterUpdateCartObserver
 *
 * @package Aheadworks\EventTickets\Observer
 */
class UpdateAttendeeOptionsAfterUpdateCartObserver implements ObserverInterface
{
    /**
     * @var AttendeeOptionProcessor
     */
    private $quoteItemAttendeeOptionProcessor;

    /**
     * @param AttendeeOptionProcessor $quoteItemAttendeeOptionProcessor
     */
    public function __construct(AttendeeOptionProcessor $quoteItemAttendeeOptionProcessor)
    {
        $this->quoteItemAttendeeOptionProcessor = $quoteItemAttendeeOptionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Checkout\Model\Cart $cart */
        $cart = $observer->getData('cart');
        $items = $cart->getQuote()->getItems();

        if ($items) {
            /** @var Item $item */
            foreach ($items as $item) {
                if ($item->isDeleted() || $item->getProductType() != EventTicket::TYPE_CODE) {
                    continue;
                }
                $this->quoteItemAttendeeOptionProcessor->processAttendeeOptions($item);
            }
        }

        return $this;
    }
}
