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
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context                  $context
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Api\DataObjectHelper              $dataObjectHelper
     * @param \Perfect\Event\Api\Data\EventInterfaceFactory        $eventFactory
     * @param \Perfect\Event\Api\EventRepositoryInterface          $eventRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Api\SearchCriteriaBuilder         $searchCriteriaBuilder
     * @param \Magento\Customer\Api\CustomerRepositoryInterface    $customerRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Perfect\Event\Api\Data\EventInterfaceFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->timezone = $timezone;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $results = [];

        if ($postValues = $this->getRequest()->getPostValue()) {
            $event = [];
            if (is_string($postValues['event'])) {
                parse_str($postValues['event'], $event);
            } elseif (is_array($postValues['event'])) {
                $event = $postValues['event'];
            }
            $eventId = (int) $event['id'];

            try {
                $event = $this->initEvent($event, $eventId);

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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function initEvent(array $eventData, int $eventId): EventInterface
    {
        try {
            $event = $this->eventRepository->get($eventId);
            $eventData['id'] = (int) $eventData['id'];
        } catch (NoSuchEntityException $exception) {
            $event = $this->eventFactory->create();
            $event->setServiceName('Стрижка');
            unset($eventData['id']);
        }

        if (!empty($eventData['services'])) {
            $services = $eventData['services'];
            unset($eventData['services']);
        }

        $this->dataObjectHelper->populateWithArray(
            $event,
            $eventData,
            EventInterface::class
        );

        if (!empty($eventData['start'])) {
            $event->setStartedAt(
                date('Y-m-d H:i:s', strtotime(preg_replace('/GMT.*$/', '', $eventData['start'])))
            );
        }
        if (!empty($eventData['end'])) {
            $event->setFinishedAt(
                date('Y-m-d H:i:s', strtotime(preg_replace('/GMT.*$/', '', $eventData['end'])))
            );
        }
        if (!empty($eventData['master'])) {
            $event->setEmployeeId($this->getEmployer($eventData['master'])->getId());
        }
        if (!empty($eventData['event_date'])
            && !empty($eventData['event_time_start'])
            && !empty($eventData['event_time_end'])
        ) {
            $eventDate = explode('/', $eventData['event_date']);
            $eventDate = sprintf('%s-%s-%s', $eventDate[2], $eventDate[1], $eventDate[0]);

            $event->setStartedAt(
                $eventDate . ' ' . $eventData['event_time_start']
            );
            $event->setFinishedAt(
                $eventDate . ' ' . $eventData['event_time_end']
            );
        }

        return $event;
    }

    /**
     * @param $name
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEmployer($name)
    {
        $this->searchCriteriaBuilder->addFilter('firstname', $name, 'eq');

        $employer = $this->customerRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return $employer ? array_shift($employer) : null;
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