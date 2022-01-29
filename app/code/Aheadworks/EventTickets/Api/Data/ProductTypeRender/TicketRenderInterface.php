<?php
namespace Aheadworks\EventTickets\Api\Data\ProductTypeRender;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TicketRenderInterface
 * @package Aheadworks\EventTickets\Api\Data\ProductTypeRender
 */
interface TicketRenderInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const QTY = 'qty';
    const TICKET_TYPE = 'ticket_type';
    const PRICE_INFO = 'price_info';
    const AVAILABLE_OPTION_UIDS = 'available_option_uids';
    const IS_ALL_PERSONAL_OPTION_EMPTY = 'is_all_personal_option_empty';
    /**#@-*/

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get type
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeInterface
     */
    public function getTicketType();

    /**
     * Set type
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeInterface $ticketType
     * @return $this
     */
    public function setTicketType($ticketType);

    /**
     * Get price info
     *
     * @return \Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface
     */
    public function getPriceInfo();

    /**
     * Set price info
     *
     * @param \Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface $priceInfo
     * @return $this
     */
    public function setPriceInfo($priceInfo);

    /**
     * Get available option unique ids
     *
     * @return string[]
     */
    public function getAvailableOptionUids();

    /**
     * Set available option unique ids
     *
     * @param string[] $availableOptionUids
     * @return $this
     */
    public function setAvailableOptionUids($availableOptionUids);

    /**
     * Get is all personal option empty
     *
     * @return bool
     */
    public function getIsAllPersonalOptionEmpty();

    /**
     * Set is all personal option empty
     *
     * @param bool $isAllPersonalOptionEmpty
     * @return $this
     */
    public function setIsAllPersonalOptionEmpty($isAllPersonalOptionEmpty);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderExtensionInterface $extAttr
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderExtensionInterface $extAttr
    );
}
