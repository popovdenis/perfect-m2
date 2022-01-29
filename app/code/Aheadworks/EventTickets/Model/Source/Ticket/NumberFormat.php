<?php
namespace Aheadworks\EventTickets\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class NumberFormat
 *
 * @package Aheadworks\EventTickets\Model\Source\Ticket
 */
class NumberFormat implements OptionSourceInterface
{
    /**#@+
     * Constants defined for number format
     */
    const ALPHANUMERIC = 'alphanumeric';
    const ALPHABETIC = 'alphabetic';
    const NUMERIC = 'numeric';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ALPHANUMERIC,
                'label' => __('Alphanumeric')
            ],
            [
                'value' => self::ALPHABETIC,
                'label' => __('Alphabetic')
            ],
            [
                'value' => self::NUMERIC,
                'label' => __('Numeric')
            ]
        ];
    }
}
