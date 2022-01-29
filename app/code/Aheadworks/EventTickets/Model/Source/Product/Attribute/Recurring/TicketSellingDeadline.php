<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class TicketSellingDeadline
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute
 */
class TicketSellingDeadline implements OptionSourceInterface
{
    /**#@+
     * Types
     */
    const EVENT_START_DATE = 'start_date';
    const IN_ADVANCE = 'in_advance';
    /**#@-*/

    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                ['value' => self::EVENT_START_DATE, 'label' => __('Event Start Date')],
                ['value' => self::IN_ADVANCE, 'label' => __('In Advance')]
            ];
        }
        return $this->options;
    }

    /**
     * Retrieve option values
     *
     * @return array
     */
    public static function getOptionValues()
    {
        return [self::EVENT_START_DATE, self::IN_ADVANCE];
    }
}
