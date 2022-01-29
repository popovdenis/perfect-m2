<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;

/**
 * Class ScheduleType
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring
 */
class ScheduleType extends AbstractSource
{
    /**#@+
     * Recurring schedule type values
     */
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
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
                [
                    'value' => self::DAILY,
                    'label' => __('Recurring day(s)')
                ],
                [
                    'value' => self::WEEKLY,
                    'label' => __('Recurring day(s) of week')
                ],
                [
                    'value' => self::MONTHLY,
                    'label' => __('Recurring day(s) of month')
                ]
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
                'type' => Table::TYPE_TEXT,
                'lenght' => 40,
                'nullable' => true,
                'comment' => 'Aheadworks Event Tickets Recurring Schedule Type',
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
