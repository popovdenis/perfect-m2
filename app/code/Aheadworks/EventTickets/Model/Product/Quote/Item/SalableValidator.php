<?php
namespace Aheadworks\EventTickets\Model\Product\Quote\Item;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Quote\ProductTicketQty as QuoteProductTicketQty;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

/**
 * Class SalableValidator
 * @package Aheadworks\EventTickets\Model\Product\Quote\Item
 */
class SalableValidator
{
    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @var QuoteProductTicketQty
     */
    private $quoteProductTicketQty;

    /**
     * @param StockManagementInterface $stockManagement
     * @param QuoteProductTicketQty $quoteProductTicketQty
     */
    public function __construct(
        StockManagementInterface $stockManagement,
        QuoteProductTicketQty $quoteProductTicketQty
    ) {
        $this->stockManagement = $stockManagement;
        $this->quoteProductTicketQty = $quoteProductTicketQty;
    }

    /**
     * Validate is quote item salable
     *
     * @param Item $quoteItem
     * @return bool
     * @throws LocalizedException
     */
    public function validate($quoteItem)
    {
        $productId = $quoteItem->getProduct()->getId();
        $websiteId = $quoteItem->getStore()->getWebsiteId();
        $sectors = $this->getSectorsQty($quoteItem);

        if ($quoteItem->getProductType() != EventTicket::TYPE_CODE) {
            return true;
        }

        if ($this->stockManagement->isTicketSellingDeadline($productId, $quoteItem)) {
            return false;
        }

        foreach ($sectors as $sectorId => $qty) {
            if (!$this->stockManagement->isAvailableTicketQtyBySector($qty, $productId, $sectorId, $quoteItem)) {
                return false;
            }
        }

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
            throw new LocalizedException($firstError->getMessage());
        }

        return true;
    }


    /**
     * Get sectors qty for quoteItem
     *
     * @param Item $quoteItem
     * @return array
     */
    private function getSectorsQty($quoteItem)
    {
        $sectors = [];
        $sectorOption = $quoteItem->getOptionByCode(OptionInterface::SECTOR_ID);

        if (!$sectorOption) {
            return $sectors;
        }

        $sectorId =  $sectorOption->getValue();
        if (isset($sectors[$sectorId])) {
            $sectors[$sectorId] += $quoteItem->getTotalQty();
        } else {
            $sectors[$sectorId] = $quoteItem->getTotalQty();
        }

        return $sectors;
    }
}
