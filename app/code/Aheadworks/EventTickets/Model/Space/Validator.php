<?php
namespace Aheadworks\EventTickets\Model\Space;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\Validator as StorefrontLabelsEntityValidator;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;

/**
 * Class Validator
 * @package Aheadworks\EventTickets\Model\Space
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
     * Returns true if and only if space entity meets the validation requirements
     *
     * @param SpaceInterface $space
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($space)
    {
        $this->_clearMessages();

        if (!$this->storefrontLabelsEntityValidator->isValid($space)) {
            $this->_addMessages($this->storefrontLabelsEntityValidator->getMessages());
            return false;
        }

        return true;
    }
}
