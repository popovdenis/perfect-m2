<?php

namespace Perfect\Service\Block\Adminhtml\Edit\Buttons;

use Perfect\Base\Block\Adminhtml\Edit\Buttons\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class AddButton
 *
 * @package Perfect\Service\Block\Adminhtml\Edit\Buttons
 */
class AddButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Add New Service'),
            'class'      => 'primary',
            'on_click'   => sprintf("location.href = '%s';", $this->getUrl('*/*/new')),
            'sort_order' => 10,
        ];
    }
}