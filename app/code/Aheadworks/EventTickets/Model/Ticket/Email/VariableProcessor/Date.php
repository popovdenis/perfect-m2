<?php
namespace Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\EmailVariables;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\EventTickets\Model\Config;

/**
 * Class Date
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor
 */
class Date implements VariableProcessorInterface
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
    public function prepareVariables($variables)
    {
        /** @var TicketInterface $ticket */
        $ticket = $variables[EmailVariables::TICKET];
        /** @var StoreInterface $store */
        $store = $variables[EmailVariables::STORE];

        $variables[EmailVariables::EVENT_START_DATE_FORMATTED] =
            $this->formatDate($ticket->getEventStartDate(), $store);
        $variables[EmailVariables::EVENT_END_DATE_FORMATTED] =
            $this->formatDate($ticket->getEventEndDate(), $store);

        if (!empty($variables[EmailVariables::TICKETS])) {
            $tickets = [];

            /** @var TicketInterface|\Aheadworks\EventTickets\Model\Ticket $ticketItem */
            foreach ($variables[EmailVariables::TICKETS] as $ticketItem) {
                $emailTicket = clone $ticketItem;
                $emailTicket[TicketInterface::EVENT_START_DATE] =
                    $this->formatDate($ticketItem->getEventStartDate(), $store);
                $emailTicket[TicketInterface::EVENT_END_DATE] =
                    $this->formatDate($ticketItem->getEventEndDate(), $store);

                $tickets[] = $emailTicket;
            }

            $variables[EmailVariables::TICKETS] = $tickets;
        }

        return $variables;
    }

    /**
     * Format date
     *
     * @param string $date
     * @param StoreInterface $store
     * @return string
     */
    private function formatDate($date, $store)
    {
        $timezone = new \DateTimeZone($store->getConfig(Config::XML_PATH_GENERAL_LOCALE_TIMEZONE));
        $localeCode = $store->getConfig(Config::XML_PATH_GENERAL_LOCALE_CODE);
        $storeDate = new \DateTime($date, new \DateTimeZone('UTC'));
        $storeDate->setTimezone($timezone);

        return $this->localeDate->formatDateTime(
            $storeDate,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM,
            $localeCode,
            $this->localeDate->getDefaultTimezone()
        );
    }
}
