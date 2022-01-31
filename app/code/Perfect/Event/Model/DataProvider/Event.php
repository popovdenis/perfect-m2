<?php

namespace Perfect\Event\Model\DataProvider;

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
     * Event constructor.
     *
     * @param                                                            $name
     * @param                                                            $primaryFieldName
     * @param                                                            $requestFieldName
     * @param \Perfect\Event\Model\ResourceModel\Event\CollectionFactory $eventCollectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface      $dataPersistor
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface            $pool
     * @param array                                                      $meta
     * @param array                                                      $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $eventCollectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $eventCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->pool = $pool;
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

        $arrItems['items'] = [];
        foreach ($this->getCollection()->getItems() as $item) {
            $arrItems['items'][] = $item->getData();
        }
        $arrItems['totalRecords'] = $this->collection->getSize();

        $this->loadedData = $arrItems;

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
}