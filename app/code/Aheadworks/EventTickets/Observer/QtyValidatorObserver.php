<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Model\Product\Quote\Item\QtyValidator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QtyValidatorObserver
 *
 * @package Aheadworks\EventTickets\Observer
 */
class QtyValidatorObserver implements ObserverInterface
{
    /**
     * @var QtyValidator $qtyValidator
     */
    private $qtyValidator;

    /**
     * @param QtyValidator $qtyValidator
     */
    public function __construct(
        QtyValidator $qtyValidator
    ) {
        $this->qtyValidator = $qtyValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();
        $this->qtyValidator->validate($quoteItem);
    }
}
