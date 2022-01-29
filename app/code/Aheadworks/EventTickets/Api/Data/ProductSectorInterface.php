<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductSectorInterface
 * @api
 */
interface ProductSectorInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const SECTOR_ID = 'sector_id';
    const QTY_AVAILABLE_TICKETS = 'qty_available_tickets';
    const SECTOR_TICKETS = 'sector_tickets';
    const SECTOR_PRODUCTS = 'sector_products';
    /**#@-*/

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
     * Get number ticket available
     *
     * @return int
     */
    public function getQtyAvailableTickets();

    /**
     * Set number ticket available
     *
     * @param int $qtyAvailableTickets
     * @return $this
     */
    public function setQtyAvailableTickets($qtyAvailableTickets);

    /**
     * Get sector tickets
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface[]
     */
    public function getSectorTickets();

    /**
     * Set sector tickets
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface[] $sectorTickets
     * @return $this
     */
    public function setSectorTickets($sectorTickets);

    /**
     * Get sector products
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface[]|null
     */
    public function getSectorProducts();

    /**
     * Set sector products
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface[] $sectorProducts
     * @return $this
     */
    public function setSectorProducts($sectorProducts);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductSectorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductSectorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductSectorExtensionInterface $extensionAttributes
    );
}
