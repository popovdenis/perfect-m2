<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Aheadworks\EventTickets\Model\Source\Product\Attribute\SpaceList;

/**
 * Class Space
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class Space implements FormatterInterface
{
    /**
     * @var SpaceList
     */
    private $spaceList;

    /**
     * @param SpaceList $spaceList
     */
    public function __construct(
        SpaceList $spaceList
    ) {
        $this->spaceList = $spaceList;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        $formattedValue = $this->spaceList->getOptionText($value);
        return $formattedValue;
    }
}
