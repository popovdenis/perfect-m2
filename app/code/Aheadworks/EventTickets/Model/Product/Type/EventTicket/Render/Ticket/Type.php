<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render\Ticket;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Type
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render\Ticket
 */
class Type extends AbstractExtensibleObject implements TypeInterface
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
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
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
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeExtensionInterface $extAttr
    ) {
        $this->_setExtensionAttributes($extAttr);
    }
}
