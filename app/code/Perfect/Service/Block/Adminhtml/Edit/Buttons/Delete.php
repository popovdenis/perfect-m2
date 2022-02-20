<?php

namespace Perfect\Service\Block\Adminhtml\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Perfect\Base\Block\Adminhtml\Edit\Buttons\GenericButton;

/**
 * Class Delete
 *
 * @package Perfect\Service\Block\Adminhtml\Edit\Buttons
 */
class Delete extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->context->getRequest()->getParam('entity_id')) {
            $data = [
                'label'      => __('Delete Service'),
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
        return $this->getUrl('*/*/delete', ['entity_id' => $this->context->getRequest()->getParam('entity_id')]);
    }
}