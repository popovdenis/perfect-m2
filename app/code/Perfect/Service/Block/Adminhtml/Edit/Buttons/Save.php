<?php

namespace Perfect\Service\Block\Adminhtml\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Perfect\Base\Block\Adminhtml\Edit\Buttons\GenericButton;

/**
 * Class Save
 *
 * @package Perfect\Service\Block\Adminhtml\Edit\Buttons
 */
class Save extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Save Service'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 300,
        ];
    }
}