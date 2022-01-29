<?php
namespace Aheadworks\EventTickets\Model\Product\Quote\Item\QtyValidator;

/**
 * Class QtyList
 *
 * @package Aheadworks\EventTickets\Model\Product\Quote\Item\QtyValidator
 */
class QtyList
{
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    private $checkedQuoteItems = [];

    /**
     * Adds item qty to checked quote items statistics array by product id and sector
     *
     * @param int $quoteId
     * @param int $quoteItemId
     * @param int $productId
     * @param int $sectorId
     * @param int $qty
     * @return int
     */
    public function addItemToQtyArray($quoteId, $quoteItemId, $productId, $sectorId, $qty)
    {
        $this->checkedQuoteItems[$quoteId][$productId][$sectorId][$quoteItemId] = $qty;
        return $qty;
    }

    /**
     * Retrieve total qty of tickets for product event within separate sector in the specific quote
     *
     * @param int $quoteId
     * @param int $productId
     * @param int $sectorId
     * @return int
     */
    public function getProductSectorTicketQty($quoteId, $productId, $sectorId)
    {
        $productSectorTicketQty = 0;
        if (isset($this->checkedQuoteItems[$quoteId][$productId][$sectorId])
            && is_array($this->checkedQuoteItems[$quoteId][$productId][$sectorId])
        ) {
            foreach ($this->checkedQuoteItems[$quoteId][$productId][$sectorId] as $quoteItemTicketQty) {
                $productSectorTicketQty += $quoteItemTicketQty;
            }
        }
        return $productSectorTicketQty;
    }

    /**
     * Retrieve total qty of tickets for product event in the specific quote
     *
     * @param int $quoteId
     * @param int $productId
     * @return int
     */
    public function getProductTicketQty($quoteId, $productId)
    {
        $productTicketQty = 0;
        if (isset($this->checkedQuoteItems[$quoteId][$productId])
            && is_array($this->checkedQuoteItems[$quoteId][$productId])
        ) {
            foreach ($this->checkedQuoteItems[$quoteId][$productId] as $sectorStatistics) {
                if (is_array($sectorStatistics)) {
                    foreach ($sectorStatistics as $quoteItemTicketQty) {
                        $productTicketQty += $quoteItemTicketQty;
                    }
                }
            }
        }
        return $productTicketQty;
    }
}
