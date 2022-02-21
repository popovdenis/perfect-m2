<?php

namespace Perfect\Service\Model\DataProvider;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Perfect\Service\Api\Data\ServiceInterface;
use Perfect\Service\Model\ResourceModel\Service\CollectionFactory;
use Perfect\Service\Model\ResourceModel\ServiceEmployee;
use Perfect\Service\Model\ResourceModel\ServicePrice;
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
    /**
     * @var \Perfect\Service\Model\ResourceModel\ServicePrice
     */
    private $servicePrice;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $serviceCollectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        CustomerRepositoryInterface $customerRepository,
        ServiceEmployee $serviceEmployee,
        ServicePrice $servicePrice,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $serviceCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->pool = $pool;
        $this->customerRepository = $customerRepository;
        $this->serviceEmployee = $serviceEmployee;
        $this->servicePrice = $servicePrice;
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

            unset($serviceData['employees']);
            $this->loadedData[$item->getId()]['service'] = $serviceData;
            $this->loadedData[$item->getId()]['prices'] = $this->getServicePrices($item->getId());
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

    protected function getServicePrices($serviceId)
    {
        $servicePrices = [];
        if ($prices = $this->servicePrice->getServicePrices($serviceId)) {
            foreach ($prices as &$price) {
                unset($price['entity_id']);
            }
            $servicePrices = [
                'price_rows' => [
                    'price_rows' => $prices
                ]
            ];
        }

        return $servicePrices;
    }
}