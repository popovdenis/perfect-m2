<?php
namespace Aheadworks\EventTickets\Model\Product\Sector;

use Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Product
 *
 * @package Aheadworks\EventTickets\Model\Product\Sector
 */
class Product extends AbstractExtensibleObject implements ProductSectorProductInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
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
        \Aheadworks\EventTickets\Api\Data\ProductSectorProductExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
