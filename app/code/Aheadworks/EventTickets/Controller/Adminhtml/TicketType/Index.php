<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\TicketType;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class Index
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\TicketType
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::ticket_types';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_EventTickets::ticket_types');
        $resultPage->getConfig()->getTitle()->prepend(__('Ticket Types'));
        return $resultPage;
    }
}
