<?php
namespace Aheadworks\EventTickets\Model\Product;

use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Sector
 *
 * @package Aheadworks\EventTickets\Model\Product
 */
class Sector extends AbstractExtensibleObject implements ProductSectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSectorId()
    {
        return $this->_get(self::SECTOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectorId($sectorId)
    {
        return $this->setData(self::SECTOR_ID, $sectorId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyAvailableTickets()
    {
        return $this->_get(self::QTY_AVAILABLE_TICKETS);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyAvailableTickets($qtyAvailableTickets)
    {
        return $this->setData(self::QTY_AVAILABLE_TICKETS, $qtyAvailableTickets);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectorTickets()
    {
        return $this->_get(self::SECTOR_TICKETS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectorTickets($sectorTickets)
    {
        return $this->setData(self::SECTOR_TICKETS, $sectorTickets);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectorProducts()
    {
        return $this->_get(self::SECTOR_PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectorProducts($sectorProducts)
    {
        return $this->setData(self::SECTOR_PRODUCTS, $sectorProducts);
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
        \Aheadworks\EventTickets\Api\Data\ProductSectorExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
