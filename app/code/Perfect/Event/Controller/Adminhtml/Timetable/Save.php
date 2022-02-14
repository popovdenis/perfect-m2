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
            $appointment = $postValues['appointment'];
            $appointmentId = (int) $appointment['id'];

            try {
                $event = $this->initEvent($appointment, $appointmentId);

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
    protected function initEvent(array $appointmentData, int $eventId): EventInterface
    {
        try {
            $appointment = $this->eventRepository->get($eventId);
        } catch (NoSuchEntityException $exception) {
            $appointment = $this->eventFactory->create();
            unset($appointmentData['id']);
        }

        $this->dataObjectHelper->populateWithArray(
            $appointment,
            $appointmentData,
            EventInterface::class
        );

        if (!empty($appointmentData['start'])) {
            $appointment->setStartedAt(
                date('Y-m-d H:i:s', strtotime(preg_replace('/GMT.*$/', '', $appointmentData['start'])))
            );
        }
        if (!empty($appointmentData['end'])) {
            $appointment->setFinishedAt(
                date('Y-m-d H:i:s', strtotime(preg_replace('/GMT.*$/', '', $appointmentData['end'])))
            );
        }
        if (!empty($appointmentData['master'])) {
            $appointment->setEmployeeId($this->getEmployer($appointmentData['master'])->getId());
        }
        if (!empty($appointmentData['appointment_date'])
            && !empty($appointmentData['appointment_time_start'])
            && !empty($appointmentData['appointment_time_end'])
        ) {
            $appointmentDate = explode('/', $appointmentData['appointment_date']);
            $appointmentDate = sprintf('%s-%s-%s', $appointmentDate[2], $appointmentDate[1], $appointmentDate[0]);

            $appointment->setStartedAt(
                $appointmentDate . ' ' . $appointmentData['appointment_time_start']
            );
            $appointment->setFinishedAt(
                $appointmentDate . ' ' . $appointmentData['appointment_time_end']
            );
        }

        return $appointment;
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