<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render\Product;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProduct\ImageInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Image
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render\Product
 */
class Image extends AbstractExtensibleObject implements ImageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSrc()
    {
        return $this->_get(self::SRC);
    }

    /**
     * {@inheritdoc}
     */
    public function setSrc($src)
    {
        return $this->setData(self::SRC, $src);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlt()
    {
        return $this->_get(self::ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlt($alt)
    {
        return $this->setData(self::ALT, $alt);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->_get(self::WIDTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setWidth($width)
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->_get(self::HEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight($height)
    {
        return $this->setData(self::HEIGHT, $height);
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
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProduct\ImageExtensionInterface $extAttr
    ) {
        $this->_setExtensionAttributes($extAttr);
    }
}
