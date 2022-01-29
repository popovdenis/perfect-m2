<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AdditionalProductOptionsInterface
 * @api
 */
interface AdditionalProductOptionsInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const QTY = 'qty';
    const SKU = 'sku';
    const RELATED_PRODUCT_ID = 'related_product_id';
    const SECTOR_ID = 'sector_id';
    const OPTION = 'option';
    /**#@-*/

    /**
     * Get qty
     *
     * @return float
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get related product id
     *
     * @return int
     */
    public function getRelatedProductId();

    /**
     * Set related product id
     *
     * @param int $relatedProductId
     * @return $this
     */
    public function setRelatedProductId($relatedProductId);

    /**
     * Get sector id
     *
     * @return int
     */
    public function getSectorId();

    /**
     * Set sector id
     *
     * @param int $sectorId
     * @return $this
     */
    public function setSectorId($sectorId);

    /**
     * Get option
     *
     * @return \Magento\Quote\Api\Data\ProductOptionInterface
     */
    public function getOption();

    /**
     * Set option
     *
     * @param \Magento\Quote\Api\Data\ProductOptionInterface $option
     * @return $this
     */
    public function setOption($option);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsExtensionInterface $extensionAttributes
    );
}
