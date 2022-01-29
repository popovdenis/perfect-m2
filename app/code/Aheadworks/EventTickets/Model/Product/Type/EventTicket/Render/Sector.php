<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Sector
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render
 */
class Sector extends AbstractExtensibleObject implements SectorRenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyAvailable()
    {
        return $this->_get(self::QTY_AVAILABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyAvailable($qtyAvailable)
    {
        return $this->setData(self::QTY_AVAILABLE, $qtyAvailable);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSalable()
    {
        return $this->_get(self::IS_SALABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSalable($isSalable)
    {
        return $this->setData(self::IS_SALABLE, $isSalable);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRange()
    {
        return $this->_get(self::PRICE_RANGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRange($priceRange)
    {
        return $this->setData(self::PRICE_RANGE, $priceRange);
    }

    /**
     * {@inheritdoc}
     */
    public function getTickets()
    {
        return $this->_get(self::TICKETS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTickets($tickets)
    {
        return $this->setData(self::TICKETS, $tickets);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalProducts()
    {
        return $this->_get(self::ADDITIONAL_PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalProducts($products)
    {
        return $this->setData(self::ADDITIONAL_PRODUCTS, $products);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsConfigurePage()
    {
        return $this->_get(self::IS_CONFIGURE_PAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsConfigurePage($isConfigurePage)
    {
        return $this->setData(self::IS_CONFIGURE_PAGE, $isConfigurePage);
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
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderExtensionInterface $extAttr
    ) {
        $this->_setExtensionAttributes($extAttr);
    }
}
