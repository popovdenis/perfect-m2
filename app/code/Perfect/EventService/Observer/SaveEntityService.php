<?php

namespace Perfect\EventService\Observer;

use Magento\Framework\Exception\NoSuchEntityException;
use Perfect\EventService\Api\Data\EventServiceInterface;
use Perfect\EventService\Api\Data\EventServiceInterfaceFactory;
use Perfect\EventService\Api\EventServiceRepositoryInterface;
use Perfect\EventService\Model\ResourceModel\EventService\CollectionFactory as EventServiceCollection;

/**
 * Class SaveEntityService
 *
 * @package Perfect\EventService\Observer
 */
class SaveEntityService implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Perfect\EventService\Model\ResourceModel\EventService\CollectionFactory
     */
    private $serviceCollectionFactory;
    /**
     * @var \Perfect\EventService\Api\EventServiceRepositoryInterface
     */
    private $serviceRepository;
    /**
     * @var \Perfect\EventService\Api\Data\EventServiceInterfaceFactory
     */
    private $serviceFactory;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * SaveEntityService constructor.
     *
     * @param \Perfect\EventService\Model\ResourceModel\EventService\CollectionFactory $serviceCollectionFactory
     * @param \Perfect\EventService\Api\EventServiceRepositoryInterface                $serviceRepository
     * @param \Perfect\EventService\Api\Data\EventServiceInterfaceFactory              $serviceFactory
     * @param \Magento\Framework\Api\DataObjectHelper                                  $dataObjectHelper
     */
    public function __construct(
        EventServiceCollection $serviceCollectionFactory,
        EventServiceRepositoryInterface $serviceRepository,
        EventServiceInterfaceFactory $serviceFactory,
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
                /** @var \Perfect\EventService\Model\ResourceModel\EventService\Collection $collection */
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
     * @return \Perfect\EventService\Api\Data\EventServiceInterface
     */
    protected function initService(array $serviceData, int $serviceId): EventServiceInterface
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
            EventServiceInterface::class
        );

        return $service;
    }
}