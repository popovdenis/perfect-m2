<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Event;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Event
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::recurring_events';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->_getSession()->setAwEtPageToReturn('aw_et_recurring_event_index');
        $this->_getSession()->setIsNeedToRedirectToProductGrid(false);

        return $this->_redirect(
            'catalog/product/edit',
            [
                'id' => $this->getRequest()->getParam('id'),
                'store' => $this->getRequest()->getParam('store')
            ]
        );
    }
}
