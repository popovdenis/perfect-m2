<?php

namespace Perfect\Service\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Perfect\Service\Api\Data\ServiceInterface;
use Perfect\Service\Api\Data\ServiceInterfaceFactory;
use Perfect\Service\Api\ServiceRepositoryInterface;
use Perfect\Service\Model\ResourceModel\ServiceEmployee;
use Perfect\Service\Model\ResourceModel\ServicePrice;

/**
 * Class EditPost
 *
 * @package Perfect\Service\Controller\Adminhtml\Index
 */
class EditPost extends \Magento\Backend\App\Action
{
    /**
     * @var \Perfect\Service\Api\ServiceRepositoryInterface
     */
    private $serviceRepository;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var \Perfect\Service\Api\Data\ServiceInterfaceFactory
     */
    private $serviceFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;
    /**
     * @var \Perfect\Service\Model\ResourceModel\ServiceEmployee
     */
    private $serviceEmployee;
    /**
     * @var \Perfect\Service\Model\ResourceModel\ServicePrice
     */
    private $servicePrice;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context                  $context
     * @param \Magento\Framework\Api\DataObjectHelper              $dataObjectHelper
     * @param \Perfect\Service\Api\Data\ServiceInterfaceFactory    $serviceFactory
     * @param \Perfect\Service\Api\ServiceRepositoryInterface      $serviceRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Perfect\Service\Model\ResourceModel\ServiceEmployee $serviceEmployee
     * @param \Perfect\Service\Model\ResourceModel\ServicePrice    $servicePrice
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        ServiceInterfaceFactory $serviceFactory,
        ServiceRepositoryInterface $serviceRepository,
        TimezoneInterface $timezone,
        ServiceEmployee $serviceEmployee,
        ServicePrice $servicePrice
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serviceFactory = $serviceFactory;
        $this->serviceRepository = $serviceRepository;
        $this->timezone = $timezone;
        $this->serviceEmployee = $serviceEmployee;
        $this->servicePrice = $servicePrice;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($postValues = $this->getRequest()->getPostValue()) {
            $service = $this->getRequest()->getParam('service');
            $employees = $this->getRequest()->getParam('employees', []);
            $prices = $this->getRequest()->getParam('prices', []);

            try {
                $service = $this->initService($service);

                $this->serviceRepository->save($service);

                $this->saveServiceMasters($service, $employees);
                $this->saveServicePrices($service, $prices);

                if ($this->getRequest()->getParam('back')) {
                    return $this->returnOnEdit($service->getId());
                }

                return $this->returnOnEntityGrid();
            } catch (LocalizedException $validationException) {
                $this->messageManager->addErrorMessage($validationException->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $serviceData
     *
     * @return \Perfect\Service\Api\Data\ServiceInterface
     */
    protected function initService(array $serviceData): ServiceInterface
    {
        $serviceId = isset($serviceData['entity_id']) ? (int) $serviceData['entity_id'] : 0;

        try {
            $service = $this->serviceRepository->get($serviceId);
        } catch (NoSuchEntityException $exception) {
            $service = $this->serviceFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $service,
            $serviceData,
            ServiceInterface::class
        );

        return $service;
    }

    /**
     * @param \Perfect\Service\Api\Data\ServiceInterface $service
     * @param                                            $masters
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveServiceMasters(ServiceInterface $service, $masters)
    {
        if (empty($masters)) {
            if ($masters = $this->serviceEmployee->getServiceMasters($service->getId())) {
                $this->serviceEmployee->deleteServiceMasters($service->getId());
            }
        } else {
            $serviceMasterMapping = [];
            $masters = array_shift($masters);
            $masters = array_shift($masters);
            foreach ($masters as $master) {
                unset($master['record_id']);
                unset($master['initialize']);
                $master['service_id'] = $service->getId();
                $serviceMasterMapping[$master['employee_id']] = $master;
            }
            $this->serviceEmployee->insertMultiple($service, $serviceMasterMapping);
        }
    }

    /**
     * @param \Perfect\Service\Api\Data\ServiceInterface $service
     * @param                                            $prices
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveServicePrices(ServiceInterface $service, $prices)
    {
        if (empty($prices)) {
            if ($prices = $this->servicePrice->getServicePrices($service->getId())) {
                $this->servicePrice->deleteServicePrices($service->getId());
            }
        } else {
            $servicePriceMapping = $servicesInOrder = [];
            $prices = array_shift($prices);
            $prices = array_shift($prices);
            foreach ($prices as $price) {
                unset($price['record_id']);
                unset($price['initialize']);
                $price['service_id'] = $service->getId();
                $servicePriceMapping[$price['employee_level']] = $price;
            }
            $this->servicePrice->insertMultiple($service, $servicePriceMapping);
        }
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
            ['entity_id' => $entityId, '_current' => true]
        );
    }
}