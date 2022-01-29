<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

/**
 * Interface FormatterInterface
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
interface FormatterInterface
{
    /**
     * Retrieve attribute value, prepared for the export
     *
     * @param int|string $value
     * @return string
     */
    public function getFormattedValue($value);
}
