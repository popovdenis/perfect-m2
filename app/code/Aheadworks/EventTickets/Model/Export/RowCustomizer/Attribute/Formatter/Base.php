<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

/**
 * Class Base
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class Base implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        $formattedValue = $value;
        return $formattedValue;
    }
}
