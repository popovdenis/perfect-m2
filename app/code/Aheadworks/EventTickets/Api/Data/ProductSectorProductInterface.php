<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductSectorProductInterface
 * @api
 */
interface ProductSectorProductInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const PRODUCT_SECTOR_ID = 'product_sector_id';
    const PRODUCT_ID = 'product_id';
    const POSITION = 'position';
    /**#@-*/

    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product ID
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductSectorProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductSectorProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductSectorProductExtensionInterface $extensionAttributes
    );
}
