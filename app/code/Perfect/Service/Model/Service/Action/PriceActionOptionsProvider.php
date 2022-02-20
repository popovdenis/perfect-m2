<?php

namespace Perfect\Service\Model\Service\Action;

/**
 * Class PriceActionOptionsProvider
 *
 * @package Perfect\Service\Model\Service\Action
 */
class PriceActionOptionsProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Apply as fixed'),
                'value' => 1
            ],
            [
                'label' => __('Apply as range'),
                'value' => 2
            ]
        ];
    }
}