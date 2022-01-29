<?php
namespace Aheadworks\EventTickets\Model\Sector;

use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class AdditionalProductOptions
 * @package Aheadworks\EventTickets\Model\Sector
 */
class AdditionalProductOptions extends AbstractExtensibleObject implements AdditionalProductOptionsInterface
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
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedProductId()
    {
        return $this->_get(self::RELATED_PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRelatedProductId($relatedProductId)
    {
        return $this->setData(self::RELATED_PRODUCT_ID, $relatedProductId);
    }

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
    public function getOption()
    {
        return $this->_get(self::OPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option)
    {
        return $this->setData(self::OPTION, $option);
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
        \Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsExtensionInterface $extensionAttributes
    ) {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
