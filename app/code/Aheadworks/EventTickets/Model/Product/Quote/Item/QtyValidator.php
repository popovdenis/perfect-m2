<?php
namespace Aheadworks\EventTickets\Model\Product\Quote\Item;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Aheadworks\EventTickets\Model\Product\Quote\Item\QtyValidator\QtyList as QuoteItemQtyList;
use Aheadworks\EventTickets\Model\Quote\ProductTicketQty as QuoteProductTicketQty;

/**
 * Class QtyValidator
 *
 * @package Aheadworks\EventTickets\Model\Product\Quote\Item
 */
class QtyValidator
{
    /**
     * Name of module, that embeds error
     */
    const ERROR_MODULE_NAME = 'aheadworks_eventtickets';

    /**
     * Qty error codes
     */
    const ERROR_QTY = 1;

    /**
     * Deadline error codes
     */
    const ERROR_DEADLINE = 2;

    /**
     * Validation error type
     */
    const ERROR_TYPE = 'qty';

    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @var QuoteItemQtyList
     */
    private $quoteItemQtyList;

    /**
     * @var QuoteProductTicketQty
     */
    private $quoteProductTicketQty;

    /**
     * @param StockManagementInterface $stockManagement
     * @param QuoteItemQtyList $quoteItemQtyList
     * @param QuoteProductTicketQty $quoteProductTicketQty
     */
    public function __construct(
        StockManagementInterface $stockManagement,
        QuoteItemQtyList $quoteItemQtyList,
        QuoteProductTicketQty $quoteProductTicketQty
    ) {
        $this->stockManagement = $stockManagement;
        $this->quoteItemQtyList = $quoteItemQtyList;
        $this->quoteProductTicketQty = $quoteProductTicketQty;
    }

    /**
     * Check product qty data
     *
     * @param Item $quoteItem
     * @return void
     */
    public function validate($quoteItem)
    {
        if ($this->isNotAvailableValidate($quoteItem)) {
            return;
        }
        $productId = $quoteItem->getProduct()->getId();
        $sectorId = $quoteItem->getOptionByCode(OptionInterface::SECTOR_ID)->getValue();
        $qty = $quoteItem->getQty();
        $this->quoteItemQtyList->addItemToQtyArray(
            $quoteItem->getQuoteId(),
            $quoteItem->getItemId(),
            $productId,
            $sectorId,
            $qty
        );
        $productSectorTicketQty = $this->quoteItemQtyList->getProductSectorTicketQty(
            $quoteItem->getQuoteId(),
            $productId,
            $sectorId
        );
        $websiteId = $quoteItem->getStore()->getWebsiteId();

        if (!$this->stockManagement->isAvailableTicketQtyBySector($qty, $productId, $sectorId, $quoteItem)
            || !$this->stockManagement->isAvailableTicketQtyBySector(
                $productSectorTicketQty,
                $productId,
                $sectorId,
                $quoteItem
            )
        ) {
            $this
                ->addErrorInfoToQuoteItem($quoteItem, __('This ticket qty is not available.'), self::ERROR_QTY)
                ->addErrorInfoToQuote($quoteItem, __('Some of the tickets qty are not available.'), self::ERROR_QTY);
        } else {
            $this->removeErrorsFromQuoteAndItem($quoteItem, self::ERROR_QTY);
        }

        if (floor($qty) !== ((float)$qty)) {
            $this->addErrorInfoToQuoteItem(
                    $quoteItem,
                    __('You cannot use decimal quantity for this product.'),
                    self::ERROR_QTY
            );
        }

        if ($this->stockManagement->isTicketSellingDeadline($productId, $quoteItem)) {
            $this
                ->addErrorInfoToQuoteItem($quoteItem, __('This product is not available.'), self::ERROR_DEADLINE)
                ->addErrorInfoToQuote($quoteItem, __('Some of the product are not available.'), self::ERROR_DEADLINE);
        } else {
            $this->removeErrorsFromQuoteAndItem($quoteItem, self::ERROR_DEADLINE);
        }

        $this->removeErrorsFromQuote(
            $quoteItem->getQuote(),
            self::ERROR_QTY,
            $this->getProductSpecificErrorOrigin($productId)
        );

        $productTicketQty = $this->quoteProductTicketQty->getTicketQtyInQuote(
            $quoteItem->getProduct(),
            $quoteItem->getQuote()
        );

        $isAvailableResult = $this->stockManagement->isTicketQtyAvailableByProduct(
            $productTicketQty,
            $productId,
            $websiteId
        );
        if ($isAvailableResult->isSalable() === false) {
            $errors = $isAvailableResult->getErrors();
            $firstError = array_shift($errors);

            $this->addErrorInfoToQuote(
                $quoteItem,
                $firstError->getMessage(),
                self::ERROR_QTY,
                $this->getProductSpecificErrorOrigin($productId)
            );
        }
    }

    /**
     * Removes error statuses from quote and item, set by this observer
     *
     * @param Item $item
     * @param int $code
     * @return void
     */
    private function removeErrorsFromQuoteAndItem($item, $code)
    {
        if ($item->getHasError()) {
            $params = ['origin' => self::ERROR_MODULE_NAME, 'code' => $code];
            $item->removeErrorInfosByParams($params);
        }

        $quote = $item->getQuote();
        if ($quote->getHasError()) {
            $quoteItems = $quote->getItemsCollection();
            $canRemoveErrorFromQuote = true;
            foreach ($quoteItems as $quoteItem) {
                if ($quoteItem->getItemId() == $item->getItemId()) {
                    continue;
                }

                $errorInfos = $quoteItem->getErrorInfos();
                foreach ($errorInfos as $errorInfo) {
                    if ($errorInfo['code'] == $code) {
                        $canRemoveErrorFromQuote = false;
                        break;
                    }
                }

                if (!$canRemoveErrorFromQuote) {
                    break;
                }
            }

            if ($canRemoveErrorFromQuote) {
                $this->removeErrorsFromQuote($quote, $code);
            }
        }
    }

    /**
     * Remove errors of specific type by its origin and code from quote
     *
     * @param Quote $quote
     * @param int $errorCode
     * @param string $origin
     * @return $this
     */
    private function removeErrorsFromQuote(
        $quote,
        $errorCode,
        $origin = self::ERROR_MODULE_NAME
    ) {
        $params = [
            'origin' => $origin,
            'code' => $errorCode
        ];
        $quote->removeErrorInfosByParams(null, $params);

        return $this;
    }

    /**
     * Check if validate available
     *
     * @param Item $quoteItem
     * @return bool
     */
    private function isNotAvailableValidate($quoteItem)
    {
        return !$quoteItem
            || !$quoteItem->getProductId()
            || $quoteItem->getProductType() != EventTicket::TYPE_CODE
            || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode();
    }

    /**
     * Add error information to Quote Item
     *
     * @param Item $quoteItem
     * @param Phrase message
     * @param int $errorCode
     * @return $this
     */
    private function addErrorInfoToQuoteItem($quoteItem, $message, $errorCode)
    {
        $quoteItem->addErrorInfo(
            self::ERROR_MODULE_NAME,
            $errorCode,
            $message
        );
        return $this;
    }

    /**
     * Add error information to Quote Item
     *
     * @param Item $quoteItem
     * @param Phrase message
     * @param int $errorCode
     * @param string $origin
     * @return $this
     */
    private function addErrorInfoToQuote($quoteItem, $message, $errorCode, $origin = self::ERROR_MODULE_NAME)
    {
        $quoteItem->getQuote()->addErrorInfo(
            self::ERROR_TYPE,
            $origin,
            $errorCode,
            $message
        );
        return $this;
    }

    /**
     * Retrieve origin for the error of specific event product
     *
     * @param int $productId
     * @return string
     */
    private function getProductSpecificErrorOrigin($productId)
    {
        return self::ERROR_MODULE_NAME . '_' . $productId;
    }
}
