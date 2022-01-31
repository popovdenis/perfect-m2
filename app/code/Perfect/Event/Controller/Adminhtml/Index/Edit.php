<?php

namespace Perfect\Event\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Edit
 *
 * @package Perfect\Event\Controller\Adminhtml\Index
 */
class Edit extends Action
{
    /**
     * @return $this|Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPageTitle = __('Add New Record');

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend($resultPageTitle);

        return $resultPage;
    }
}
