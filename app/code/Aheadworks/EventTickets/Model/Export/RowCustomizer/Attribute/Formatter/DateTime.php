<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class DateTime
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class DateTime implements FormatterInterface
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        $formattedValue = '';

        try {
            if ($value) {
                $formattedValue = $this->localeDate->formatDateTime(
                    new \DateTime($value),
                    \IntlDateFormatter::SHORT,
                    \IntlDateFormatter::SHORT
                );
            }
        } catch (\Exception $exception) {
        }

        return $formattedValue;
    }
}
