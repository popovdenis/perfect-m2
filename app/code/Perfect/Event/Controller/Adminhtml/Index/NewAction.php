<?php

namespace Perfect\Event\Controller\Adminhtml\Index;

/**
 * Class NewAction
 *
 * @package Perfect\Event\Controller\Adminhtml\Index
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Execute method.
     *
     * @return null
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}