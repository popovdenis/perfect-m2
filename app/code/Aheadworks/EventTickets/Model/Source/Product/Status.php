<?php
namespace Aheadworks\EventTickets\Model\Source\Product;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\EventTickets\Model\Source\Product
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Event product statuses
     */
    const PAST = 1;
    const RUNNING = 2;
    const UPCOMING = 3;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PAST, 'label' => __('Past')],
            ['value' => self::RUNNING, 'label' => __('Running')],
            ['value' => self::UPCOMING, 'label' => __('Upcoming')]
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
