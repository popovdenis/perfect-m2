<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

use Magento\Framework\Escaper;

/**
 * Class AttendeeOptions
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Renderer
 */
class AttendeeOptions implements RendererInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Escaper $escaper
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function render($options)
    {
        $result = [];
        if (empty($options->getAwEtAttendees())) {
            return $result;
        }

        $attendees = [];
        foreach ($options->getAwEtAttendees() as $attendee) {
            $attendeeId = $attendee->getAttendeeId();
            $value = !empty($attendee->getValue()) ? $attendee->getValue() : __('Not Specified');
            $attendees[$attendeeId][] = implode(
                ' : ',
                [$attendee->getLabel(), $this->escaper->escapeHtml($value)]
            );
        }

        $result = [];
        foreach ($attendees as $attendeeId => $attendee) {
            $result[] = [
                'label' => __('Attendee %1', $attendeeId),
                'value' => implode(', ', $attendee),
                'print_value' => implode("\r\n", $attendee),
                'custom_view' => true
            ];
        }

        return $result;
    }
}
