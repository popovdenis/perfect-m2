<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Space;

use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Space
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::spaces';

    /**
     * @var SpaceRepositoryInterface
     */
    private $spaceRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param SpaceRepositoryInterface $spaceRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        SpaceRepositoryInterface $spaceRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->spaceRepository = $spaceRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit space
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->spaceRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the space')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_EventTickets::spaces')
            ->getConfig()->getTitle()->prepend(
                $id ? __('Edit Space') : __('New Space')
            );

        return $resultPage;
    }
}
