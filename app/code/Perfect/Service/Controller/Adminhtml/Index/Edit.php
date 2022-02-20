<?php

namespace Perfect\Service\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Event\Api\EventRepositoryInterface;

/**
 * Class Edit
 *
 * @package Perfect\Service\Controller\Adminhtml\Index
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Perfect_Event::perfect_service_edit';

    /**
     * @var \Perfect\Event\Api\EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context         $context
     * @param \Perfect\Event\Api\EventRepositoryInterface $eventRepository
     */
    public function __construct(
        Context $context,
        EventRepositoryInterface $eventRepository
    )
    {
        parent::__construct($context);
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($entityId = (int)$this->getRequest()->getParam('entity_id')) {
            try {
                $entity = $this->getEntityById($entityId);

                $resultPage->getConfig()->getTitle()->prepend(__('Edit service %1', $entity->getServiceName()));
            } catch (NoSuchEntityException | LocalizedException $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());

                return $this->getResultRedirectFactory()->setPath('perfect_service/*/*');
            }
        }

        return $resultPage;
    }

    /**
     * @param int $entityId
     *
     * @return \Perfect\Event\Api\Data\EventInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getEntityById(int $entityId)
    {
        return $this->eventRepository->get($entityId);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function getResultRedirectFactory()
    {
        return $this->resultRedirectFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}