<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\TicketType;

use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class NewAction
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\TicketType
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::ticket_types';

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
