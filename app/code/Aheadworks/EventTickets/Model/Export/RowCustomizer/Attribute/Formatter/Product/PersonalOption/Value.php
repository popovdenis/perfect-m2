<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Product\PersonalOption;

use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\FormatterInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;

/**
 * Class Value
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Product\PersonalOption
 */
class Value implements FormatterInterface
{
    /**
     * Retrieve attribute value, prepared for the export
     *
     * @param ProductPersonalOptionValueInterface $value
     * @return string
     */
    public function getFormattedValue($value)
    {
        try {
            $formattedValue = $value->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
            $formattedValue = '';
        }
        return $formattedValue;
    }
}
