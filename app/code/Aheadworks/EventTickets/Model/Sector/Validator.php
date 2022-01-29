<?php
namespace Aheadworks\EventTickets\Model\Sector;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\Validator as StorefrontLabelsEntityValidator;
use Aheadworks\EventTickets\Api\Data\SectorInterface;

/**
 * Class Validator
 * @package Aheadworks\EventTickets\Model\Sector
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
     * Returns true if and only if sector entity meets the validation requirements
     *
     * @param SectorInterface $sector
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($sector)
    {
        $this->_clearMessages();

        if (!$this->storefrontLabelsEntityValidator->isValid($sector)) {
            $this->_addMessages($this->storefrontLabelsEntityValidator->getMessages());
            return false;
        }

        return true;
    }
}
