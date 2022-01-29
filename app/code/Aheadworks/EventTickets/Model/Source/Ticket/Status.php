<?php
namespace Aheadworks\EventTickets\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\EventTickets\Model\Source\Ticket
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Ticket statuses
     */
    const UNUSED    = 1;
    const USED      = 2;
    const CANCELED  = 3;
    const PENDING   = 4;
    /**#@-*/

    /**#@+
     * Keys of the config array for change status actions
     */
    const CONFIG_STATUS             = 'status';
    const CONFIG_LABEL              = 'label';
    const CONFIG_CONFIRM_TITLE      = 'confirmTitle';
    const CONFIG_CONFIRM_MESSAGE    = 'confirmMessage';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::UNUSED, 'label' => __('Unused')],
            ['value' => self::USED, 'label' => __('Used')],
            ['value' => self::CANCELED, 'label' => __('Canceled')],
            ['value' => self::PENDING, 'label' => __('Pending')]
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
