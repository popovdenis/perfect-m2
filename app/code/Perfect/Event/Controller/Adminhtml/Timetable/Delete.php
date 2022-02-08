<?php

namespace Perfect\Event\Controller\Adminhtml\Timetable;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Event\Api\Data\EventInterface;
use Perfect\Event\Api\EventRepositoryInterface;

class Delete extends \Magento\Backend\App\Action
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
        $results = [];

        if ($postValues = $this->getRequest()->getPostValue()) {
            $appointment = $postValues['appointment'];
            $eventId = (int) $appointment['id'];

            try {
                $event = $this->eventRepository->get($eventId);

                $this->eventRepository->delete($event);
            } catch (LocalizedException $validationException) {
                $this->messageManager->addErrorMessage($validationException->getMessage());
            }
        }

        return $this->goBack($results);
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