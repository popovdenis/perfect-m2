<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Event;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Index
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Event
 */
class Index extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::recurring_events';

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
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_EventTickets::recurring_events');
        $resultPage->getConfig()->getTitle()->prepend(__('Recurring Events'));

        return $resultPage;
    }
}
