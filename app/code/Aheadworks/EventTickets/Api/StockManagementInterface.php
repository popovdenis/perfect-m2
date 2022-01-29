<?php
namespace Aheadworks\EventTickets\Api;

/**
 * Interface StockManagementInterface
 * @api
 */
interface StockManagementInterface
{
    /**
     * Retrieves available ticket qty
     *
     * @param int $productId
     * @return int
     */
    public function getAvailableTicketQty($productId);

    /**
     * Retrieves available ticket qty by event ticket product object
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     */
    public function getAvailableProductTicketQty($product);

    /**
     * Retrieves available ticket qty by sector
     *
     * @param int $productId
     * @param int $sectorId
     * @param \Magento\Quote\Api\Data\CartItemInterface|null $quoteItem
     * @return int
     */
    public function getAvailableTicketQtyBySector($productId, $sectorId, $quoteItem = null);

    /**
     * Retrieve ticket status by sector
     *
     * @param int $productId
     * @param int $sectorId
     * @return int
     */
    public function getTicketSectorStatus($productId, $sectorId);

    /**
     * Retrieve ticket status
     *
     * @param int $productId
     * @return int
     */
    public function getTicketStatus($productId);

    /**
     * Retrieve ticket status by event ticket product object
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     */
    public function getProductTicketStatus($product);

    /**
     * Check if available ticket qty by sector
     *
     * @param int $qty
     * @param int $productId
     * @param int $sectorId
     * @param \Magento\Quote\Api\Data\CartItemInterface|null $quoteItem
     * @return bool
     */
    public function isAvailableTicketQtyBySector($qty, $productId, $sectorId, $quoteItem = null);

    /**
     * Check if total ticket qty is available by event product
     *
     * @param int $totalQtyOfProductTickets
     * @param int $productId
     * @param int $websiteId
     * @return \Aheadworks\EventTickets\Api\Data\IsAvailableResultInterface
     */
    public function isTicketQtyAvailableByProduct($totalQtyOfProductTickets, $productId, $websiteId);

    /**
     * Check if it's ticket selling deadline time
     *
     * @param int $productId
     * @param \Magento\Quote\Api\Data\CartItemInterface|null $quoteItem
     * @return bool
     */
    public function isTicketSellingDeadline($productId, $quoteItem = null);

    /**
     * Check if it's ticket selling deadline time by event ticket product object
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Quote\Api\Data\CartItemInterface|null $quoteItem
     * @return bool
     */
    public function isProductTicketSellingDeadline($product, $quoteItem = null);

    /**
     * Check if ticket salable
     *
     * @param int $productId
     * @return bool
     */
    public function isSalable($productId);

    /**
     * Check if ticket salable by event ticket product object
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     */
    public function isProductSalable($product);

    /**
     * Check if ticket salable
     *
     * @param int $productId
     * @param int $sectorId
     * @return bool
     */
    public function isSalableBySector($productId, $sectorId);
}
