<?php

namespace Perfect\Service\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Service\Api\Data\ServiceInterface;
use Perfect\Service\Api\ServiceRepositoryInterface;

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
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context                  $context
     * @param \Magento\Framework\Api\DataObjectHelper              $dataObjectHelper
     * @param \Perfect\Service\Api\Data\ServiceInterfaceFactory    $serviceFactory
     * @param \Perfect\Service\Api\ServiceRepositoryInterface      $serviceRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Perfect\Service\Api\Data\ServiceInterfaceFactory $serviceFactory,
        ServiceRepositoryInterface $serviceRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serviceFactory = $serviceFactory;
        $this->serviceRepository = $serviceRepository;
        $this->timezone = $timezone;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($postValues = $this->getRequest()->getPostValue()) {
            $service = $postValues['service'];

            try {
                $service = $this->initService($service);

                $this->serviceRepository->save($service);

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