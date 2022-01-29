<?php
namespace Aheadworks\EventTickets\Model\StorefrontLabelsEntity;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\EventTickets\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use Aheadworks\EventTickets\Model\StorefrontLabels\Validator as StorefrontLabelsValidator;

/**
 * Class Validator
 * @package Aheadworks\EventTickets\Model\StorefrontLabelsEntity
 */
class Validator extends AbstractValidator
{
    /**
     * @var StorefrontLabelsValidator
     */
    private $storefrontLabelsValidator;

    /**
     * @param StorefrontLabelsValidator $storefrontLabelsValidator
     */
    public function __construct(
        StorefrontLabelsValidator $storefrontLabelsValidator
    ) {
        $this->storefrontLabelsValidator = $storefrontLabelsValidator;
    }

    /**
     * Returns true if and only if entity that contains storefront labels meets the validation requirements
     *
     * @param StorefrontLabelsEntityInterface $storefrontLabelsEntity
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($storefrontLabelsEntity)
    {
        $this->_clearMessages();

        return ($this->isLabelsDataValid($storefrontLabelsEntity));
    }

    /**
     * Returns true if and only if storefront labels data is correct
     *
     * @param StorefrontLabelsEntityInterface $storefrontLabelsEntity
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    private function isLabelsDataValid($storefrontLabelsEntity)
    {
        $isAllStoreViewsDataPresents = false;
        $labelsStoreIds = [];
        if ($storefrontLabelsEntity->getLabels() && (is_array($storefrontLabelsEntity->getLabels()))) {
            /** @var \Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface $labelsRecord */
            foreach ($storefrontLabelsEntity->getLabels() as $labelsRecord) {
                if (!in_array($labelsRecord->getStoreId(), $labelsStoreIds)) {
                    array_push($labelsStoreIds, $labelsRecord->getStoreId());
                } else {
                    $this->_addMessages(['Duplicated store view in storefront descriptions found.']);
                    return false;
                }
                if ($labelsRecord->getStoreId() == StoreOptions::ALL_STORE_VIEWS) {
                    $isAllStoreViewsDataPresents = true;
                }

                if (!$this->storefrontLabelsValidator->isValid($labelsRecord)) {
                    $this->_addMessages($this->storefrontLabelsValidator->getMessages());
                    return false;
                }
            }
        }
        if (!$isAllStoreViewsDataPresents) {
            $this->_addMessages(
                ['Default values of storefront descriptions (for All Store Views option) aren\'t set.']
            );
            return false;
        }
        return true;
    }
}
