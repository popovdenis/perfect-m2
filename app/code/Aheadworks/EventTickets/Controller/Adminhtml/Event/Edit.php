<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Event;

/**
 * Class Edit
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Event
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::events';

    /**
     * Edit action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_getSession()->setAwEtPageToReturn('aw_et_event_index');
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
