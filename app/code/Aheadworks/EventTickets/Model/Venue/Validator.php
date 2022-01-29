<?php
namespace Aheadworks\EventTickets\Model\Venue;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\Validator as StorefrontLabelsEntityValidator;
use Aheadworks\EventTickets\Api\Data\VenueInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\EventTickets\Model\Venue
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
     * Returns true if and only if venue entity meets the validation requirements
     *
     * @param VenueInterface $venue
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($venue)
    {
        $this->_clearMessages();

        if (!$this->storefrontLabelsEntityValidator->isValid($venue)) {
            $this->_addMessages($this->storefrontLabelsEntityValidator->getMessages());
            return false;
        }

        return true;
    }
}
