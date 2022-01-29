<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Aheadworks\EventTickets\Model\Source\Product\Attribute\VenueList;

/**
 * Class Venue
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class Venue implements FormatterInterface
{
    /**
     * @var VenueList
     */
    private $venueList;

    /**
     * @param VenueList $venueList
     */
    public function __construct(
        VenueList $venueList
    ) {
        $this->venueList = $venueList;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        $formattedValue = $this->venueList->getOptionText($value);
        return $formattedValue;
    }
}
