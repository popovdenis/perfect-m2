<?php

namespace Perfect\Service\Model\DataProvider;

use Perfect\Service\Model\ResourceModel\Service\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class ServicesList
 *
 * @package Perfect\Service\Model\DataProvider
 */
class ServicesList extends AbstractDataProvider
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

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $serviceCollectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $serviceCollectionFactory->create();
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