<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Venue;

use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Venue
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::venues';

    /**
     * @var VenueRepositoryInterface
     */
    private $venueRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param VenueRepositoryInterface $venueRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        VenueRepositoryInterface $venueRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->venueRepository = $venueRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit venue
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->venueRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the venue')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_EventTickets::venues')
            ->getConfig()->getTitle()->prepend(
                $id ? __('Edit Venue') : __('New Venue')
            );

        return $resultPage;
    }
}
