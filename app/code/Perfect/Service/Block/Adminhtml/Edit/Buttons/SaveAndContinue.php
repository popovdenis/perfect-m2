<?php

namespace Perfect\Service\Block\Adminhtml\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Perfect\Base\Block\Adminhtml\Edit\Buttons\GenericButton;

/**
 * Class SaveAndContinue
 *
 * @package Perfect\Service\Block\Adminhtml\Edit\Buttons
 */
class SaveAndContinue extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Save and Continue Edit'),
            'class'          => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order'     => 200,
        ];
    }
}