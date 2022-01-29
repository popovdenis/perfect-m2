<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;

/**
 * Class ScheduleType
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute
 */
class ScheduleType extends AbstractSource
{
    /**#@+
     * Schedule type
     */
    const ONE_TIME = 1;
    const RECURRING = 2;
    /**#@-*/

    /**
     * @var AttributeFactory
     */
    private $eavAttributeFactory;

    /**
     * @param AttributeFactory $eavAttributeFactory
     */
    public function __construct(
        AttributeFactory $eavAttributeFactory
    ) {
        $this->eavAttributeFactory = $eavAttributeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::ONE_TIME, 'label' => __('One-Time Event')],
                ['value' => self::RECURRING, 'label' => __('Recurring Event')]
            ];
        }
        return $this->_options;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => true,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aheadworks Event Tickets Schedule Type',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->eavAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
