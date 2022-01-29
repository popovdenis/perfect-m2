<?php
namespace Aheadworks\EventTickets\Model\Product\PersonalOptions\Config;

use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 *
 * @package Aheadworks\EventTickets\Model\Product\PersonalOptions\Config
 */
class Converter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $output = [];

        /** @var $optionNode \DOMNode */
        foreach ($source->getElementsByTagName('option') as $optionNode) {
            $optionName = $this->getAttributeValue($optionNode, 'name');
            $data = [];
            $data['name'] = $optionName;
            $data['label'] = $this->getAttributeValue($optionNode, 'label');

            /** @var $childNode \DOMNode */
            foreach ($optionNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $inputTypeName = $this->getAttributeValue($childNode, 'name');
                $data['types'][$inputTypeName] = [
                    'name' => $inputTypeName,
                    'label' => $this->getAttributeValue($childNode, 'label')
                ];
            }
            $output[$optionName] = $data;
        }
        return $output;
    }

    /**
     * Get attribute value
     *
     * @param \DOMNode $node
     * @param string $attributeName
     * @param string|null $defaultValue
     * @return null|string
     */
    private function getAttributeValue(\DOMNode $node, $attributeName, $defaultValue = null)
    {
        $attributeNode = $node->attributes->getNamedItem($attributeName);
        $output = $defaultValue;
        if ($attributeNode) {
            $output = $attributeNode->nodeValue;
        }
        return $output;
    }
}
