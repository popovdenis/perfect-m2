<?php
namespace Aheadworks\EventTickets\Model\Source\Email;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 *
 * @package Aheadworks\EventTickets\Model\Source\Email
 */
class Status implements ArrayInterface
{
    /**#@+
     * Email Status values
     */
    const SENT = 1;
    const READY_FOR_SENDING = 2;
    const FAILED = 3;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SENT,
                'label' => __('Sent')
            ],
            [
                'value' => self::READY_FOR_SENDING,
                'label' => __('Ready for Sending')
            ],
            [
                'value' => self::FAILED,
                'label' => __('Failed')
            ]
        ];
    }
}
