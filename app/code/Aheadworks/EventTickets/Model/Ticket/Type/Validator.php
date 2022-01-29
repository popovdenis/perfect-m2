<?php
namespace Aheadworks\EventTickets\Model\Ticket\Type;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\Validator as StorefrontLabelsEntityValidator;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Type
 */
class Validator extends AbstractValidator
{
    /**
     * @var StorefrontLabelsEntityValidator
     */
    private $storefrontLabelsEntityValidator;

    /**
     * @param StorefrontLabelsEntityValidator $storefrontLabelsEntityValidator
     */
    public function __construct(
        StorefrontLabelsEntityValidator $storefrontLabelsEntityValidator
    ) {
        $this->storefrontLabelsEntityValidator = $storefrontLabelsEntityValidator;
    }

    /**
     * Returns true if and only if ticket type entity meets the validation requirements
     *
     * @param TicketTypeInterface $ticketType
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($ticketType)
    {
        $this->_clearMessages();

        if (!$this->storefrontLabelsEntityValidator->isValid($ticketType)) {
            $this->_addMessages($this->storefrontLabelsEntityValidator->getMessages());
            return false;
        }

        return true;
    }
}
