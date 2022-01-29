<?php
namespace Aheadworks\EventTickets\Plugin\Model\Quote;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Magento\Quote\Model\Quote\Config;

/**
 * Class ConfigProductAttributes
 * @package Aheadworks\EventTickets\Plugin\Model\Quote
 */
class ConfigProductAttributesPlugin
{
    /**
     * Append additional product attribute keys to quote item collection
     *
     * @param Config $subject
     * @param array $attributeKeys
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductAttributes(Config $subject, array $attributeKeys)
    {
        $attributes = [ProductAttributeInterface::CODE_AW_ET_EXCLUSIVE_PRODUCT];
        foreach ($attributes as $attribute) {
            if (!in_array($attribute, $attributeKeys)) {
                $attributeKeys[] = $attribute;
            }
        }
        return $attributeKeys;
    }
}
