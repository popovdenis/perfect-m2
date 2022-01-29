<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\TicketType;

use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class Edit
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\TicketType
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::ticket_types';

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TicketTypeRepositoryInterface $ticketTypeRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->ticketTypeRepository = $ticketTypeRepository;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->ticketTypeRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the ticket type')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_EventTickets::ticket_types')
            ->getConfig()->getTitle()->prepend(
                $id ? __('Edit Ticket Type') : __('New Ticket Type')
            );

        return $resultPage;
    }
}
