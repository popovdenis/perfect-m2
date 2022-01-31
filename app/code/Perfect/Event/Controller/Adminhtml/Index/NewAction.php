<?php

namespace Perfect\Event\Controller\Adminhtml\Index;

use Magento\Backend\App\AbstractAction;

/**
 * Class NewAction
 *
 * @package Perfect\Event\Controller\Adminhtml\Index
 */
class NewAction extends AbstractAction
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