<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Model\Product\Quote\Item\AttendeeOptionProcessor;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Class UpdateAttendeeOptionsAfterAddProductToCartObserver
 *
 * @package Aheadworks\EventTickets\Observer
 */
class UpdateAttendeeOptionsAfterAddProductToCartObserver implements ObserverInterface
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
        /** @var Item $item */
        $item = $observer->getData('quote_item');
        if ($item->getProductType() == EventTicket::TYPE_CODE) {
            $this->quoteItemAttendeeOptionProcessor->processAttendeeOptions($item, false);
        }

        return $this;
    }
}
