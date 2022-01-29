<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Model\Product\PersonalOptions\Config as PersonalOptionsConfig;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\Validator as StorefrontLabelsEntityValidator;

/**
 * Class PersonalOptions
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class PersonalOptions extends AbstractBackend
{
    /**
     * @var PersonalOptionsConfig
     */
    private $personalOptionsConfig;

    /**
     * @var StorefrontLabelsEntityValidator
     */
    private $storefrontLabelsEntityValidator;

    /**
     * @param PersonalOptionsConfig $personalOptionsConfig
     * @param StorefrontLabelsEntityValidator $storefrontLabelsEntityValidator
     */
    public function __construct(
        PersonalOptionsConfig $personalOptionsConfig,
        StorefrontLabelsEntityValidator $storefrontLabelsEntityValidator
    ) {
        $this->personalOptionsConfig = $personalOptionsConfig;
        $this->storefrontLabelsEntityValidator = $storefrontLabelsEntityValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $addedKeys = [];
        /** @var ProductPersonalOptionInterface[] $options */
        $options = !empty($object->getExtensionAttributes()->getAwEtPersonalOptions())
            ? $object->getExtensionAttributes()->getAwEtPersonalOptions()
            : [];
        foreach ($options as $option) {
            $optionType = $option->getType();
            $addedKeys[$optionType] = $this->processValidateDuplicate($optionType, $addedKeys);
            $this->processValidateStorefrontLabel($option);

            if ($this->isAllowSaveOptionValues($option) && is_array($option->getValues())) {
                foreach ($option->getValues() as $value) {
                    $this->processValidateStorefrontLabel($value);
                }
            }
        }

        return true;
    }

    /**
     * Validate on duplicate
     *
     * @param string $key
     * @param array $addedKeys
     * @return bool
     * @throws LocalizedException
     */
    private function processValidateDuplicate($key, $addedKeys)
    {
        $defaultTypes = $this->personalOptionsConfig
            ->getTypesByGroup(ProductPersonalOptionInterface::OPTION_GROUP_DEFAULT);

        if (in_array($key, $defaultTypes) && array_key_exists($key, $addedKeys)) {
            throw new LocalizedException(__('Duplicate default option type found.'));
        }
        return true;
    }

    /**
     * Storefront label validate
     *
     * @param StorefrontLabelsEntityInterface $entity
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function processValidateStorefrontLabel($entity)
    {
        if (!$this->storefrontLabelsEntityValidator->isValid($entity)) {
            $messages = $this->storefrontLabelsEntityValidator->getMessages();
            throw new LocalizedException(__(reset($messages)));
        }
    }

    /**
     * Check if allow save option values
     *
     * @param ProductPersonalOptionInterface $entity
     * @return bool
     */
    private function isAllowSaveOptionValues($entity)
    {
        $selectTypeOptions = $this->personalOptionsConfig
            ->getTypesByGroup(ProductPersonalOptionInterface::OPTION_GROUP_SELECT);

        return in_array($entity->getType(), $selectTypeOptions);
    }
}
