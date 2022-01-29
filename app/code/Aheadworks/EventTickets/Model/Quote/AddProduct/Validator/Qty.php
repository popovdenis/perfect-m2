<?php
namespace Aheadworks\EventTickets\Model\Quote\AddProduct\Validator;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Model\Stock\Validator\IsCorrectQtyCondition;
use Aheadworks\EventTickets\Model\Quote\ProductTicketQty as QuoteProductTicketQty;

/**
 * Class Qty
 * @package Aheadworks\EventTickets\Model\Quote\AddProduct\Validator
 */
class Qty implements ValidatorInterface
{
    /**
     * @var IsCorrectQtyCondition
     */
    private $isCorrectQtyCondition;

    /**
     * @var QuoteProductTicketQty
     */
    private $quoteProductTicketQty;

    /**
     * Qty constructor.
     * @param IsCorrectQtyCondition $isCorrectQtyCondition
     * @param QuoteProductTicketQty $quoteProductTicketQty
     */
    public function __construct(
        IsCorrectQtyCondition $isCorrectQtyCondition,
        QuoteProductTicketQty $quoteProductTicketQty
    ) {
        $this->isCorrectQtyCondition = $isCorrectQtyCondition;
        $this->quoteProductTicketQty = $quoteProductTicketQty;
    }

    /**
     * @inheritdoc
     */
    public function validate($quote, $product, $request)
    {
        if (!$request instanceof DataObject) {
            throw new LocalizedException(__('We found an invalid request for adding product to quote.'));
        }

        $websiteId = $quote->getStore()->getWebsiteId();
        $ticketRequestedQty = $this->quoteProductTicketQty->getTicketQtyInAddProductRequest($product, $request);
        $ticketQtyInQuote = $this->quoteProductTicketQty->getTicketQtyInQuote($product, $quote);

        if (!$ticketRequestedQty) {
            throw new LocalizedException(__('Please specify qty.'));
        }

        $isCorrectQtyConditionResult = $this->isCorrectQtyCondition->execute(
            $ticketRequestedQty + $ticketQtyInQuote,
            $product->getSku(),
            $websiteId
        );

        $errors = $isCorrectQtyConditionResult->getErrors();
        if ($errors && is_array($errors)) {
            $error = array_shift($errors);
            throw new LocalizedException($error->getMessage());
        }
    }
}
