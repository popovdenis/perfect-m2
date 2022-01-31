<?php

namespace Perfect\Event\Block\Adminhtml\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class AddButton
 *
 * @package Perfect\Event\Block\Adminhtml\Edit\Buttons
 */
class AddButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Add New Event'),
            'class'      => 'primary',
            'on_click'   => sprintf("location.href = '%s';", $this->getUrl('*/*/new')),
            'sort_order' => 10,
        ];
    }
}