<?php

namespace Perfect\Event\Controller\Adminhtml\Index;

use Exception;
use Perfect\Event\Api\EventRepositoryInterface;

/**
 * Class Delete
 *
 * @package Perfect\Event\Controller\Adminhtml\Index
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Perfect\Event\Api\EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var \Perfect\Event\Api\Data\EventInterfaceFactory
     */
    private $eventFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context         $context
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory
     * @param \Perfect\Event\Api\EventRepositoryInterface $eventRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        EventRepositoryInterface $eventRepository
    ) {
        parent::__construct($context);
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $appointmentId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($appointmentId) {
            try {
                $this->eventRepository->delete($appointmentId);

                $this->messageManager->addSuccessMessage(__('The appointment has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $appointmentId]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find an appointment to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}