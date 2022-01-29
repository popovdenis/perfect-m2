<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Stock;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\EventTickets\Model\Source\Product\Stock
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Constants defined for stock status
     */
    const SOLD_OUT = 1;
    const FULL = 2;
    const AVAILABLE = 3;
    const CAPACITY = 4;
    const UNAVAILABLE = 5;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::AVAILABLE, 'label' => __('Available')],
            ['value' => self::SOLD_OUT, 'label' => __('Sold Out')],
            ['value' => self::CAPACITY, 'label' => __('Capacity')],
            ['value' => self::FULL, 'label' => __('Full')],
            ['value' => self::UNAVAILABLE, 'label' => __('Unavailable')]
        ];
    }

    /**
     * Retrieve option by value
     *
     * @param int $value
     * @return string|null
     */
    public function getOptionLabelByValue($value)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return null;
    }
}
