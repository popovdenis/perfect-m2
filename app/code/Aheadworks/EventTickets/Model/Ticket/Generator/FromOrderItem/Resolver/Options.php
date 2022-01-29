<?php
namespace Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver;

use Aheadworks\EventTickets\Api\Data\AttendeeInterface;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class Options
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver
 */
class Options
{
    /**
     * Default option value if is empty
     *
     * @var string
     */
    const DEFAULT_OPTION_VALUE = 'Not specified';

    /**
     * @var TicketOptionInterfaceFactory
     */
    private $ticketOptionFactory;

    /**
     * @param TicketOptionInterfaceFactory $ticketOptionFactory
     */
    public function __construct(TicketOptionInterfaceFactory $ticketOptionFactory)
    {
        $this->ticketOptionFactory = $ticketOptionFactory;
    }

    /**
     * Resolve ticket options
     *
     * @param OptionInterface $options
     * @param int $qtyNumber
     * @return TicketOptionInterface[]
     */
    public function resolve($options, $qtyNumber)
    {
        $ticketOptions = [];
        $attendees = $options->getAwEtAttendees();
        if (!empty($attendees)) {
            $ticketOptions = $this->resolveAttendeeOptions($attendees, $qtyNumber);
        }

        return $ticketOptions;
    }

    /**
     * Resolve attendee options
     *
     * @param AttendeeInterface[] $attendees
     * @param int $qtyNumber
     * @return TicketOptionInterface[]
     */
    private function resolveAttendeeOptions($attendees, $qtyNumber)
    {
        $ticketOptions = [];
        foreach ($attendees as $attendee) {
            if ($attendee->getAttendeeId() != $qtyNumber) {
                continue;
            }

            $name = $this->resolveOptionLabel($attendee->getProductOption()->getLabels(), $attendee->getLabel());
            /** @var TicketOptionInterface $ticketOption */
            $ticketOption = $this->ticketOptionFactory->create();
            $ticketOption
                ->setName($name)
                ->setType($attendee->getProductOption()->getType())
                ->setValue($this->resolveOptionValue($attendee));
            $ticketOptions[] = $ticketOption;
        }

        return $ticketOptions;
    }

    /**
     * Resolve option label by default store
     *
     * @param StorefrontLabelsInterface[] $labels
     * @param string $default
     * @return string
     */
    private function resolveOptionLabel($labels, $default)
    {
        foreach ($labels as $label) {
            if ($label->getStoreId() == Store::DEFAULT_STORE_ID) {
                return $label->getTitle();
            }
        }

        return $default;
    }

    /**
     * Resolve option value
     *
     * @param AttendeeInterface $attendee
     * @return string
     */
    private function resolveOptionValue($attendee)
    {
        $currentValue = $attendee->getValue();
        if ($attendee->getProductOption()->getType() == ProductPersonalOptionInterface::OPTION_TYPE_DATE) {
            $currentValue = $this->prepareDateOptionValue($attendee->getValue());
        }
        $defaultValue = self::DEFAULT_OPTION_VALUE;
        return empty($currentValue) ? $defaultValue : $currentValue;
    }

    /**
     * Prepare value for date option
     *
     * @param string $dateOptionValue
     * @return string
     */
    private function prepareDateOptionValue($dateOptionValue)
    {
        $preparedDate = '';
        if (!empty($dateOptionValue)) {
            $date = new \DateTime($dateOptionValue, new \DateTimeZone('UTC'));
            $preparedDate = $date->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }
        return $preparedDate;
    }
}
