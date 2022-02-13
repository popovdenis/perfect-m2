<?php

namespace Perfect\Event\Model\DataProvider;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Perfect\Event\Model\ResourceModel\Event\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class PerfectEvent
 *
 * @package Perfect\Event\Model\DataProvider
 */
class Event extends AbstractDataProvider
{
    /**
     * @var \Perfect\Event\Model\ResourceModel\Event\Collection
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

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $eventCollectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        CustomerRepositoryInterface $customerRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $eventCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->pool = $pool;
        $this->customerRepository = $customerRepository;
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

        foreach ($this->getCollection()->getItems() as $item) {
            $this->loadedData[$item->getId()]['appointment'] = $item->getData();
            $this->loadedData[$item->getId()]['appointment']['worker'] = $this->getWorker($item->getEmployeeId());
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

    /**
     * @param $workerId
     *
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getWorker($workerId)
    {
        if ($customer = $this->customerRepository->getById($workerId)) {
            return $customer->getFirstname();
        }

        return null;
    }
}