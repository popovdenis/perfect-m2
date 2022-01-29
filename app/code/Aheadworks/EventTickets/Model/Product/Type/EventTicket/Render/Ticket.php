<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Ticket
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render
 */
class Ticket extends AbstractExtensibleObject implements TicketRenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketType()
    {
        return $this->_get(self::TICKET_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTicketType($ticketType)
    {
        return $this->setData(self::TICKET_TYPE, $ticketType);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceInfo()
    {
        return $this->_get(self::PRICE_INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceInfo($priceInfo)
    {
        return $this->setData(self::PRICE_INFO, $priceInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableOptionUids()
    {
        return $this->_get(self::AVAILABLE_OPTION_UIDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOptionUids($availableOptionUids)
    {
        return $this->setData(self::AVAILABLE_OPTION_UIDS, $availableOptionUids);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAllPersonalOptionEmpty()
    {
        return $this->_get(self::IS_ALL_PERSONAL_OPTION_EMPTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAllPersonalOptionEmpty($isAllPersonalOptionEmpty)
    {
        return $this->setData(self::IS_ALL_PERSONAL_OPTION_EMPTY, $isAllPersonalOptionEmpty);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderExtensionInterface $extAttr
    ) {
        $this->_setExtensionAttributes($extAttr);
    }
}
