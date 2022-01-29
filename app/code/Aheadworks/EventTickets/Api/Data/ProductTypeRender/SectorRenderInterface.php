<?php
namespace Aheadworks\EventTickets\Api\Data\ProductTypeRender;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SectorRenderInterface
 * @package Aheadworks\EventTickets\Api\Data\ProductTypeRender
 */
interface SectorRenderInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const QTY_AVAILABLE = 'qty_available';
    const STATUS = 'status';
    const IS_SALABLE = 'is_salable';
    const PRICE_RANGE = 'price_range';
    const TICKETS = 'tickets';
    const ADDITIONAL_PRODUCTS = 'additional_products';
    const IS_CONFIGURE_PAGE = 'is_configure_page';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get product name
     *
     * @return string
     */
    public function getName();

    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get available qty
     *
     * @return int
     */
    public function getQtyAvailable();

    /**
     * Set available qty
     *
     * @param int $qtyAvailable
     * @return $this
     */
    public function setQtyAvailable($qtyAvailable);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get is salable
     *
     * @return bool
     */
    public function getIsSalable();

    /**
     * Set is salable
     *
     * @param bool $isSalable
     * @return $this
     */
    public function setIsSalable($isSalable);

    /**
     * Get price range
     *
     * @return string
     */
    public function getPriceRange();

    /**
     * Set price range
     *
     * @param string $priceRange
     * @return $this
     */
    public function setPriceRange($priceRange);

    /**
     * Get tickets
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterface[]
     */
    public function getTickets();

    /**
     * Set tickets
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterface[] $tickets
     * @return $this
     */
    public function setTickets($tickets);

    /**
     * Get tickets
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderInterface[]
     */
    public function getAdditionalProducts();

    /**
     * Set tickets
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderInterface[] $products
     * @return $this
     */
    public function setAdditionalProducts($products);

    /**
     * Get is configure page
     *
     * @return bool
     */
    public function getIsConfigurePage();

    /**
     * Set is configure page
     *
     * @param bool $isConfigurePage
     * @return $this
     */
    public function setIsConfigurePage($isConfigurePage);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderExtensionInterface $extAttr
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderExtensionInterface $extAttr
    );
}
