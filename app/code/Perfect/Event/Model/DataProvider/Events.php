<?php

namespace Perfect\Event\Model\DataProvider;

use Magento\Framework\Api\Search\SearchCriteria;

/**
 * Class Events
 *
 * @package Perfect\Event\Model\DataProvider
 */
class Events extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Data Provider name
     *
     * @var string
     */
    protected $name;
    /**
     * Data Provider Primary Identifier name
     *
     * @var string
     */
    protected $primaryFieldName;
    /**
     * Provider configuration data
     *
     * @var array
     */
    protected $data = [];
    /**
     * @var array
     */
    protected $meta;
    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var SearchCriteria
     */
    protected $searchCriteria;
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->primaryFieldName = $primaryFieldName;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get Data Provider name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get config data
     *
     * @return mixed
     */
    public function getConfigData()
    {
        return $this->data['config'] ?? [];
    }

    /**
     * Set config data
     *
     * @param mixed $config
     *
     * @return void
     */
    public function setConfigData($config)
    {
        $this->data['config'] = $config;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param string $fieldSetName
     * @param string $fieldName
     *
     * @return array
     */
    public function getFieldMetaInfo($fieldSetName, $fieldName)
    {
        return $this->meta[$fieldSetName]['children'][$fieldName] ?? [];
    }

    /**
     * Get field Set meta info
     *
     * @param string $fieldSetName
     *
     * @return array
     */
    public function getFieldSetMetaInfo($fieldSetName)
    {
        return $this->meta[$fieldSetName] ?? [];
    }

    /**
     * @param string $fieldSetName
     *
     * @return array
     */
    public function getFieldsMetaInfo($fieldSetName)
    {
        return $this->meta[$fieldSetName]['children'] ?? [];
    }

    /**
     * Get primary field name
     *
     * @return string
     */
    public function getPrimaryFieldName()
    {
        return $this->primaryFieldName;
    }

    /**
     * Get field name in request
     *
     * @return string
     */
    public function getRequestFieldName()
    {
        // TODO: Implement getRequestFieldName() method.
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        $arrItems = ['items' => []];

        $arrItems['totalRecords'] = 0;

        return $arrItems;
    }

    /**
     * Add field filter to collection
     *
     * @param \Magento\Framework\Api\Filter $filter
     *
     * @return mixed
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->searchCriteriaBuilder->addFilter($filter);
    }

    /**
     * Add ORDER BY to the end or to the beginning
     *
     * @param string $field
     * @param string $direction
     *
     * @return void
     */
    public function addOrder($field, $direction)
    {
        $this->searchCriteriaBuilder->addSortOrder($field, $direction);
    }

    /**
     * Set Query limit
     *
     * @param int $offset
     * @param int $size
     *
     * @return void
     */
    public function setLimit($offset, $size)
    {
        $this->searchCriteriaBuilder->setPageSize($size);
        $this->searchCriteriaBuilder->setCurrentPage($offset);
    }

    /**
     * Returns search criteria
     *
     * @return \Magento\Framework\Api\Search\SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }
        return $this->searchCriteria;
    }

    /**
     * @return \Magento\Framework\Api\Search\SearchResultInterface|\Magento\Framework\Api\SearchResultsInterface
     */
    public function getSearchResult()
    {
        return $this->searchResultsFactory->create();
    }
}