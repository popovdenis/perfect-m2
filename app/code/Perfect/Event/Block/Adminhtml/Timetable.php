<?php

namespace Perfect\Event\Block\Adminhtml;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Perfect\Event\Api\Data\EventInterface;

/**
 * Class Timetable
 *
 * @package Perfect\Event\Block\Adminhtml
 */
class Timetable extends \Magento\Backend\Block\Template
{
    const MASTER_CUSTOMER_GROUP = 'Сотрудник';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonEncoder;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Perfect\Event\Api\EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var \Perfect\Event\Helper\Customer
     */
    private $customerHelper;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Timetable constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Framework\Serialize\Serializer\Json                     $jsonEncoder
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                     $searchCriteriaBuilder
     * @param \Perfect\Event\Api\EventRepositoryInterface                      $eventRepository
     * @param \Perfect\Event\Helper\Customer                                   $customerHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                $customerRepository
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Perfect\Event\Api\EventRepositoryInterface $eventRepository,
        \Perfect\Event\Helper\Customer $customerHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->jsonEncoder = $jsonEncoder;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->eventRepository = $eventRepository;
        $this->customerHelper = $customerHelper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param                                              $schedulerId
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     *
     * @return bool|false|string
     */
    public function getConfig($schedulerId, $customer)
    {
        $config = [
            'schedulerId' => $schedulerId,
            'scheduler' => 'scheduler' . $schedulerId,
            'appointments' => $this->getAppointments($customer),
            'searchConfig' => [
                'url' => $this->getUrl('perfect_event/timetable/search', ['_secure' => true])
            ],
            'appointmentModal' => '.appointment-modal',
        ];

        return $this->jsonEncoder->serialize($config);
    }

    /**
     * Get collection of appointments by customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAppointments($customer)
    {
        $searchBuilder = $this->searchCriteriaBuilder;
        $searchBuilder->addFilter(EventInterface::EMPLOYEE_ID, $customer->getId(), 'eq');

        $entities = $this->eventRepository->getEntities($searchBuilder->create())->getItems();
        $appointments = [];
        if ($entities) {
            /**@var \Perfect\Event\Api\Data\EventInterface $entity */
            foreach ($entities as $entity) {
                $appointments[$entity->getId()] = $entity->getData();
                $appointments[$entity->getId()]['master'] = $customer->getFirstname();

                $client = $this->getClientInfo($entity->getClientId());
                $appointments[$entity->getId()]['client'] = [
                    'client_id' => $client->getId(),
                    'client_name' => $client->getFirstname() . ' ' . $client->getLastname(),
                    'client_phone' => $client->getCustomAttribute('phone')->getValue(),
                    'client_email' => $client->getEmail(),
                ];
            }
        }

        return $appointments;
    }

    /**
     * @param $clientId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getClientInfo($clientId)
    {
        return $this->customerRepository->getById($clientId);
    }

    /**
     * Get employees collection
     */
    public function getEmployees()
    {
        if ($customerGroupId = $this->getEmployeeGroup()) {
            $customerCollection = $this->collectionFactory->create();
            $customerCollection->addFieldToFilter('group_id', ['eq' => $customerGroupId]);

            return $customerCollection->getItems();
        }

        return [];
    }

    /**
     * @return int|null
     */
    protected function getEmployeeGroup()
    {
        try {
            /** @var \Magento\Customer\Api\Data\GroupInterface $workerGroup */
            $workerGroup = $this->customerHelper->getCustomerGroupByName(self::MASTER_CUSTOMER_GROUP);

            return $workerGroup->getId();
        } catch (LocalizedException $e) {
        }

        return null;
    }
}