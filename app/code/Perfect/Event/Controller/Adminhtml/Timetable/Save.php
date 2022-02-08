<?php

namespace Perfect\Event\Controller\Adminhtml\Timetable;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Event\Api\Data\EventInterface;
use Perfect\Event\Api\EventRepositoryInterface;

/**
 * Class Save
 *
 * @package Perfect\Event\Controller\Adminhtml\Timetable
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
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
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Api\DataObjectHelper              $dataObjectHelper
     * @param \Perfect\Event\Api\Data\EventInterfaceFactory        $eventFactory
     * @param \Perfect\Event\Api\EventRepositoryInterface          $eventRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Perfect\Event\Api\Data\EventInterfaceFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->timezone = $timezone;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create(ResultFactory::TYPE_JSON);
        $results = [];

        if ($postValues = $this->getRequest()->getPostValue()) {
            $appointment = $postValues['appointment'];
            $eventId = (int) $appointment['id'];
            $start = preg_replace('/GMT.*$/', '', $appointment['started_at']);
            $end = preg_replace('/GMT.*$/', '', $appointment['finished_at']);

            try {
                $eventData = [
                    EventInterface::SUBJECT => $appointment['subject'],
                    EventInterface::DESCRIPTION => $appointment['description'],
                    EventInterface::STARTED_AT => date('Y-m-d H:i:s', strtotime($start)),
                    EventInterface::FINISHED_AT => date('Y-m-d H:i:s', strtotime($end)),
                    EventInterface::WORKER_ID => 3,
                ];
                $event = $this->initEvent($eventData, $eventId);

                $this->eventRepository->save($event);

                $results = $event->getData();
            } catch (CouldNotSaveException $validationException) {
                $this->messageManager->addErrorMessage($validationException->getMessage());
            }
        }

        return $this->goBack($results);
    }

    /**
     * @param array $eventData
     * @param int   $eventId
     *
     * @return \Perfect\Event\Api\Data\EventInterface
     */
    protected function initEvent(array $eventData, int $eventId): EventInterface
    {
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

        return $event;
    }

    /**
     * @param array $events
     *
     * @return string
     */
    private function goBack($events)
    {
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($events);
    }
}