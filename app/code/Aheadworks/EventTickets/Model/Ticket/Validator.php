<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Validator\IsUnique as TicketIsUnique;

/**
 * Class Validator
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Validator extends AbstractValidator
{
    /**
     * @var TicketIsUnique
     */
    private $ticketIsUniqueValidator;

    /**
     * @param TicketIsUnique $ticketIsUniqueValidator
     */
    public function __construct(
        TicketIsUnique $ticketIsUniqueValidator
    ) {
        $this->ticketIsUniqueValidator = $ticketIsUniqueValidator;
    }

    /**
     * Returns true if and only if ticket entity meets the validation requirements
     *
     * @param TicketInterface $ticket
     * @return bool
     * @throws \Exception
     */
    public function isValid($ticket)
    {
        $this->_clearMessages();

        if (!$ticket->getId() && !$this->ticketIsUniqueValidator->validate($ticket->getNumber())) {
            $this->_addMessages(['Ticket number ' . $ticket->getNumber() . ' already exists.']);
        }

        return empty($this->getMessages());
    }
}
