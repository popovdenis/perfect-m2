<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Locale\TranslatedLists;

/**
 * Class WeekDays
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring
 */
class WeekDays implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $dayCodeName = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday'
    ];

    /**
     * @var TranslatedLists
     */
    private $translatedList;

    /**
     * @param TranslatedLists $translatedLists
     */
    public function __construct(TranslatedLists $translatedLists)
    {
        $this->translatedList = $translatedLists;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->translatedList->getOptionWeekdays();
    }

    /**
     * Retrieve day name by code
     *
     * @param int $code
     * @return string|null
     */
    public function getDayNameByCode($code)
    {
        return isset($this->dayCodeName[$code])
            ? $this->dayCodeName[$code]
            : null;
    }

    /**
     * Retrieve option values
     *
     * @return array
     */
    public function getOptionValues()
    {
        return array_keys($this->dayCodeName);
    }
}
