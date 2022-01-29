<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Option\Configuration;

use Aheadworks\EventTickets\Model\Product\Additional\Option\ConfigurationInterface;
use Magento\ConfigurableProduct\Api\Data\ConfigurableItemOptionValueInterface;

/**
 * Class Configurable
 * @package Aheadworks\EventTickets\Model\Product\Additional\Option\Configuration
 */
class Configurable implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function processOptions($data)
    {
        $options = [];
        if (isset($data['super_attribute']) && is_array($data['super_attribute'])) {
            foreach ($data['super_attribute'] as $optionId => $optionValue) {
                $options[] = [
                    ConfigurableItemOptionValueInterface::OPTION_ID => (string)$optionId,
                    ConfigurableItemOptionValueInterface::OPTION_VALUE => (int)$optionValue
                ];
            }
            $options = ['configurable_item_options' => $options];
        }
        return $options;
    }
}
