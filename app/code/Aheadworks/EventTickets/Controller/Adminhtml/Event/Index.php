<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Event;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class Index
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Event
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::events';

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
        $resultPage->setActiveMenu('Aheadworks_EventTickets::events');
        $resultPage->getConfig()->getTitle()->prepend(__('Events'));
        return $resultPage;
    }
}
