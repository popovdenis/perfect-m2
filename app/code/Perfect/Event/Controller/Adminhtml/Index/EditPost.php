<?php

namespace Perfect\Event\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Event\Api\Data\EventInterface;
use Perfect\Event\Api\EventRepositoryInterface;

/**
 * Class EditPost
 *
 * @package Perfect\Event\Controller\Adminhtml\Index
 */
class EditPost extends \Magento\Backend\App\Action
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
     * @param \Magento\Backend\App\Action\Context                  $context
     * @param \Magento\Framework\Api\DataObjectHelper              $dataObjectHelper
     * @param \Perfect\Event\Api\Data\EventInterfaceFactory        $eventFactory
     * @param \Perfect\Event\Api\EventRepositoryInterface          $eventRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Perfect\Event\Api\Data\EventInterfaceFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->timezone = $timezone;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($postValues = $this->getRequest()->getPostValue()) {
            $appointment = $postValues['appointment'];


            try {
                $event = $this->initEvent($appointment);

                $this->eventRepository->save($event);

                if ($this->getRequest()->getParam('back')) {
                    return $this->returnOnEdit($event->getId());
                }

                return $this->returnOnEntityGrid();
            } catch (LocalizedException $validationException) {
                $this->messageManager->addErrorMessage($validationException->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $eventData
     *
     * @return \Perfect\Event\Api\Data\EventInterface
     */
    protected function initEvent(array $eventData): EventInterface
    {
        $eventId = isset($appointment['id']) ? (int) $eventData['id'] : 0;

        try {
            $event = $this->eventRepository->get($eventId);
        } catch (NoSuchEntityException $exception) {
            $event = $this->eventFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $event,
            $eventData,
            EventInterface::class
        );

        $startedAt = strtotime(preg_replace('/GMT.*$/', '', $eventData['started_at']));
        $finishedAt = strtotime(preg_replace('/GMT.*$/', '', $eventData['finished_at']));

        $event->setStartedAt(date('Y-m-d H:i:s', $startedAt));
        $event->setFinishedAt(date('Y-m-d H:i:s', $finishedAt));

        return $event;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function getResultRedirectFactory()
    {
        return $this->resultRedirectFactory->create();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function returnOnEntityGrid()
    {
        return $this->getResultRedirectFactory()->setPath('*/*');
    }

    /**
     * Return on Entity Edit page.
     *
     * @param $entityId
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function returnOnEdit($entityId)
    {
        return $this->getResultRedirectFactory()->setPath(
            '*/*/edit',
            ['id' => $entityId, '_current' => true]
        );
    }
}