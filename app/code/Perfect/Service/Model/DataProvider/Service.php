<?php

namespace Perfect\Service\Model\DataProvider;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Perfect\Service\Api\Data\ServiceInterface;
use Perfect\Service\Model\ResourceModel\Service\CollectionFactory;
use Perfect\Service\Model\ResourceModel\ServiceEmployee;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class Service extends AbstractDataProvider
{
    /**
     * @var \Perfect\Service\Model\ResourceModel\Service\Collection
     */
    protected $collection;
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var \Magento\Ui\DataProvider\Modifier\PoolInterface
     */
    private $pool;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Perfect\Service\Model\ResourceModel\ServiceEmployee
     */
    private $serviceEmployee;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $serviceCollectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        CustomerRepositoryInterface $customerRepository,
        ServiceEmployee $serviceEmployee,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $serviceCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->pool = $pool;
        $this->customerRepository = $customerRepository;
        $this->serviceEmployee = $serviceEmployee;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     *
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var ServiceInterface $item */
        foreach ($this->getCollection()->getItems() as $item) {
            $serviceData = $item->getData();
            $serviceData['service_duration'] = [
                'service_duration_h' => $item->getServiceDurationH(),
                'service_duration_m' => $item->getServiceDurationM(),
            ];
            unset($serviceData['employees']);
            $this->loadedData[$item->getId()]['service'] = $serviceData;
            $this->loadedData[$item->getId()]['employees'] = $this->getServiceMasters($item->getId());
        }

        return $this->loadedData;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var \Magento\Ui\DataProvider\Modifier\ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    protected function getServiceMasters($serviceId)
    {
        $serviceMasters = [];
        if ($masters = $this->serviceEmployee->getServiceMasters($serviceId)) {
            foreach ($masters as &$master) {
                unset($master['entity_id']);
            }
            $serviceMasters = [
                'masters' => [
                    'masters' => $masters
                ]
            ];
        }

        return $serviceMasters;
    }
}