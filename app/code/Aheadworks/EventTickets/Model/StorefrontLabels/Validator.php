<?php
namespace Aheadworks\EventTickets\Model\StorefrontLabels;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;

class Validator extends AbstractValidator
{
    /**
     * Returns true if and only if entity meets the validation requirements
     *
     * @param StorefrontLabelsInterface $storefrontLabels
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($storefrontLabels)
    {
        $this->_clearMessages();

        if (!\Zend_Validate::is($storefrontLabels->getTitle(), 'NotEmpty')) {
            $this->_addMessages(['Storefront title is required.']);
        }

        return empty($this->getMessages());
    }
}
