<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class TimeSlot
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Renderer
 */
class TimeSlot implements RendererInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param TimezoneInterface $localeDate
     * @param Escaper $escaper
     */
    public function __construct(
        TimezoneInterface $localeDate,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        $this->localeDate = $localeDate;
    }

    /**
     * @inheritdoc
     */
    public function render($options)
    {
        $result = [];
        if (!$options->getAwEtRecurringTimeSlotId()) {
            return $result;
        }
        $result[] = [
            'label' => __('Start Date'),
            'value' => $this->escaper->escapeHtml($this->formatDate($options->getAwEtRecurringStartDate()))
        ];
        $result[] = [
            'label' => __('End Date'),
            'value' => $this->escaper->escapeHtml($this->formatDate($options->getAwEtRecurringEndDate()))
        ];

        return $result;
    }

    /**
     * Format date
     *
     * @param string $date
     * @return string
     */
    private function formatDate($date)
    {
        return $this->localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::SHORT,
            null,
            $this->localeDate->getDefaultTimezone()
        );
    }
}
