<?php

namespace Perfect\Event\Block\Adminhtml\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Delete
 *
 * @package Perfect\Event\Block\Adminhtml\Edit\Buttons
 */
class Delete extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->context->getRequest()->getParam('id')) {
            $data = [
                'label'      => __('Delete Event'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \''
                    . $this->getDeleteUrl() . '\')',
                'sort_order' => 100,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->context->getRequest()->getParam('id')]);
    }
}