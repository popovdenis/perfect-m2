<?php
namespace Aheadworks\EventTickets\Model\Ticket\Option;

use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Option
 */
class Resolver
{
    /**
     * Prefix for option key generation
     */
    const OPTION_KEY_PREFIX = 'ticket_option_';

    /**
     * Check if field relates to the custom ticket option
     *
     * @param string $fieldName
     * @return bool
     */
    public function checkIfFieldIsTicketOption($fieldName)
    {
        return (strpos($fieldName, self::OPTION_KEY_PREFIX) !== false);
    }

    /**
     * Generate key for specified ticket option
     *
     * @param TicketOptionInterface $ticketOption
     * @return string
     */
    public function generateOptionKey($ticketOption)
    {
        $preparedTicketOptionName =
            $this->getPreparedTicketOptionName($ticketOption->getName());
        $optionKey = self::OPTION_KEY_PREFIX
            . $preparedTicketOptionName . '_'
            . $ticketOption->getType();

        return $optionKey;
    }

    /**
     * Retrieve ticket option name prepared for using in the field name
     *
     * @param $ticketOptionName
     * @return string
     */
    private function getPreparedTicketOptionName($ticketOptionName)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $ticketOptionName);
    }
}
