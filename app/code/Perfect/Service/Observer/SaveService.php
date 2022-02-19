<?php

namespace Perfect\Service\Observer;

use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\Service\Api\Data\ServiceInterface;
use Perfect\Service\Api\Data\ServiceInterfaceFactory;
use Perfect\Service\Api\ServiceRepositoryInterface;
use Perfect\Service\Model\ResourceModel\Service\CollectionFactory as ServiceCollection;

/**
 * Class SaveService
 *
 * @package Perfect\Service\Observer
 */
class SaveService implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Perfect\Service\Model\ResourceModel\Service\CollectionFactory
     */
    private $serviceCollectionFactory;
    /**
     * @var \Perfect\Service\Api\ServiceRepositoryInterface
     */
    private $serviceRepository;
    /**
     * @var \Perfect\Service\Api\Data\ServiceInterfaceFactory
     */
    private $serviceFactory;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * SaveEntityService constructor.
     *
     * @param \Perfect\Service\Model\ResourceModel\Service\CollectionFactory $serviceCollectionFactory
     * @param \Perfect\Service\Api\ServiceRepositoryInterface                $serviceRepository
     * @param \Perfect\Service\Api\Data\ServiceInterfaceFactory              $serviceFactory
     * @param \Magento\Framework\Api\DataObjectHelper                                  $dataObjectHelper
     */
    public function __construct(
        ServiceCollection $serviceCollectionFactory,
        ServiceRepositoryInterface $serviceRepository,
        ServiceInterfaceFactory $serviceFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        $this->serviceCollectionFactory = $serviceCollectionFactory;
        $this->serviceRepository = $serviceRepository;
        $this->serviceFactory = $serviceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $entity = $observer->getEntity();
        $request = $observer->getRequest();

        if (!empty($request['services'])) {
            if ($services = $request['services']) {
                /** @var \Perfect\Service\Model\ResourceModel\Service\Collection $collection */
                $collection = $this->serviceCollectionFactory->create();
                $collection->addFieldToFilter('event_id', $entity->getId());
                $oldServices = $collection->getItems();

                if (empty($oldServices)) {
                    $this->addServices($services, $entity->getId());
                } else {
                    foreach ($oldServices as $index1 => $oldService) {
                        foreach ($services as $index2 => $service) {
                            if ($oldService->getServiceName() === $service['service_name']
                                && $oldService->getServiceQuantity() == $service['service_quantity']
                            ) {
                                unset($oldServices[$index1]);
                                unset($services[$index2]);
                            }
                        }
                    }
                    if ($oldServices) {
                        // delete old services
                        $this->deleteServices($oldServices);
                    }
                    if ($services) {
                        // add new services
                        $this->addServices($services, $entity->getId());
                    }
                }
            }
        }
    }

    /**
     * @param array $services
     * @param       $entityId
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function addServices(array $services, $entityId)
    {
        foreach ($services as $serviceData) {
            $serviceId = empty($serviceData['id']) ? 0 : (int) $serviceData['id'];
            $service = $this->initService($serviceData, $serviceId);
            $service->setEventId($entityId);
            $this->serviceRepository->save($service);
        }
    }

    /**
     * @param array $services
     */
    protected function deleteServices(array $services)
    {
        $ids = [];
        foreach ($services as $service) {$ids[] = $service->getId();}
        $this->serviceRepository->deleteByIds($ids);
    }

    /**
     * @param array $serviceData
     * @param int   $serviceId
     *
     * @return \Perfect\Service\Api\Data\ServiceInterface
     */
    protected function initService(array $serviceData, int $serviceId): ServiceInterface
    {
        try {
            $service = $this->serviceRepository->get($serviceId);
        } catch (NoSuchEntityException $exception) {
            $service = $this->serviceFactory->create();
            unset($serviceData['id']);
        }

        $this->dataObjectHelper->populateWithArray(
            $service,
            $serviceData,
            ServiceInterface::class
        );

        return $service;
    }
}