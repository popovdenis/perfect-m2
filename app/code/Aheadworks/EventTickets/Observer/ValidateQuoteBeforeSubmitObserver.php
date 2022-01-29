<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Model\Product\Quote\Item\SalableValidator;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ValidateQuoteBeforeSubmitObserver
 *
 * @package Magento\CatalogInventory\Observer
 */
class ValidateQuoteBeforeSubmitObserver implements ObserverInterface
{
    /**
     * @var SalableValidator
     */
    private $salableValidator;

    /**
     * @param SalableValidator $salableValidator
     */
    public function __construct(SalableValidator $salableValidator)
    {
        $this->salableValidator = $salableValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $errorMessage = __('Not all of your event ticket products are available in the requested quantity.');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getAwEtProcessed()) {
            return $this;
        }
        foreach ($quote->getAllItems() as $quoteItem) {
            if (!$this->salableValidator->validate($quoteItem)) {
                throw new LocalizedException($errorMessage);
            }
        }

        $quote->setAwEtProcessed(true);

        return $this;
    }
}
