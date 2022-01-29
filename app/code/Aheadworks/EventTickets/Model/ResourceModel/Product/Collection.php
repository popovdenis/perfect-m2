<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket as EventTicketProductType;
use Aheadworks\EventTickets\Model\Product\Status\Resolver as EventProductStatusResolver;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Magento\Store\Model\Store;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitationFactory;
use Magento\Framework\DB\Select;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product
 */
class Collection extends ProductCollection
{
    /**
     * Keys for additional data of collection items
     */
    const STATUS_FIELD_KEY = 'aw_et_status';
    const TOTAL_TICKETS_QTY_FIELD_KEY = 'aw_et_total_tickets_qty';
    const USED_TICKETS_QTY_FIELD_KEY = 'aw_et_used_tickets_qty';
    const AVAILABLE_TICKETS_QTY_FIELD_KEY = 'aw_et_available_tickets_qty';
    const TMP_TABLE_ALIAS = 'tmp_table';

    /**
     * @var EventProductStatusResolver
     */
    protected $eventProductStatusResolver;

    /**
     * @var []
     */
    protected $storeIds = [];

    /**
     * @var int
     */
    protected $websiteId;

    /**
     * Need to add event status flag
     *
     * @var bool
     */
    protected $needToAddEventStatus;

    /**
     * @var string[]
     */
    protected $linkageTableNames = [];

    /**
     * @var string[]
     */
    protected $ticketsStatisticsFields = [
        self::TOTAL_TICKETS_QTY_FIELD_KEY,
        self::USED_TICKETS_QTY_FIELD_KEY,
        self::AVAILABLE_TICKETS_QTY_FIELD_KEY
    ];

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param EventProductStatusResolver $eventProductStatusResolver
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param ProductLimitationFactory|null $productLimitationFactory
     * @param \Magento\Framework\EntityManager\MetadataPool|null $metadataPool
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        EventProductStatusResolver $eventProductStatusResolver,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ProductLimitationFactory $productLimitationFactory = null,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool = null
    ) {
        $this->eventProductStatusResolver = $eventProductStatusResolver;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection,
            $productLimitationFactory,
            $metadataPool
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this
            ->addFieldToFilter(ProductInterface::TYPE_ID, ['eq' => EventTicketProductType::TYPE_CODE])
            ->addAttributeToFilter(
                ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE,
                [
                    ['null' => ''],
                    ['eq' => ScheduleType::ONE_TIME]
                ],
                'left'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'website_id') {
            $websiteId = $this->saveAppliedWebsite($condition);
            $this->addWebsiteFilter($websiteId);
            return $this;
        }
        if (in_array($field, $this->ticketsStatisticsFields)) {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add status filter
     *
     * @param int $statusId
     * @return $this
     */
    public function addStatusFilter($statusId)
    {
        $this->eventProductStatusResolver->addStatusFilter($this, $statusId);
        return $this;
    }

    /**
     * Set is need to add event status flag
     *
     * @param bool $needToAddEventStatus
     */
    public function setNeedToAddEventStatus($needToAddEventStatus)
    {
        $this->needToAddEventStatus = $needToAddEventStatus;
    }

    /**
     * Get is need to add event status flag
     *
     * @return bool
     */
    public function getNeedToAddEventStatus()
    {
        return $this->needToAddEventStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (in_array($attribute, $this->ticketsStatisticsFields)) {
            $this->joinTicketsStatisticsColumns();
            $this->getSelect()->order($attribute . ' ' . $dir);
            return $this;
        }
        return parent::addAttributeToSort($attribute, $dir);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeLoad()
    {
        $this->addEventFieldsIfNeeded();
        return parent::_beforeLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->applySavedWebsite();
        if ($this->getNeedToAddEventStatus()) {
            $this->doAddEventStatus();
        }
        $this->attachTicketsStatisticsColumns();
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        foreach ($this->ticketsStatisticsFields as $ticketsStatisticsField) {
            if ($this->getFilter($ticketsStatisticsField)) {
                $this->joinTicketsStatisticsColumns();
                break;
            }
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Save applied in the filter website and its stores
     *
     * @param array $filterCondition
     * @return int
     */
    private function saveAppliedWebsite($filterCondition)
    {
        $this->websiteId = $filterCondition['eq'];
        try {
            /** @var \Magento\Store\Api\Data\WebsiteInterface $website */
            $website = $this->_storeManager->getWebsite($this->websiteId);
            $websiteStoreIds = $website->getStoreIds();
        } catch (\Exception $exception) {
            $websiteStoreIds = [];
        }
        $this->storeIds = array_merge($websiteStoreIds, [Store::DEFAULT_STORE_ID]);

        return $this->websiteId;
    }

    /**
     * Apply saved website to the collection items
     *
     * @return $this
     */
    private function applySavedWebsite()
    {
        if ($this->websiteId) {
            foreach ($this as $product) {
                $product->setData('website_id', $this->websiteId);
            }
        }
        return $this;
    }

    /**
     * Add event status to the collection items
     *
     * @return $this
     */
    private function doAddEventStatus()
    {
        foreach ($this as $product) {
            $productStatus = $this->eventProductStatusResolver->getProductStatus($product);
            $product->setData(self::STATUS_FIELD_KEY, $productStatus);
        }
        return $this;
    }

    /**
     * Add if needed event-specific fields to select
     *
     * @return $this
     */
    private function addEventFieldsIfNeeded()
    {
        if ($this->getNeedToAddEventStatus()) {
            $this->addFieldToSelect(ProductAttributeInterface::CODE_AW_ET_START_DATE);
            $this->addFieldToSelect(ProductAttributeInterface::CODE_AW_ET_END_DATE);
        }
        return $this;
    }

    /**
     * Retrieve query for tickets statistics calculation
     * @todo query logic improvement to prevent possible problems with performance
     *
     * @return Select
     */
    protected function getTicketsStatisticsQuery()
    {
        $usedTicketsQtyExpression = new \Zend_Db_Expr('(' . $this->getUsedTicketsQtyQuery(). ')');

        $availableTicketsQtyExpression = '
            (
                SUM(sector_table.tickets_qty) - ' .
                'IFNULL(
                    used_tickets_qty_subselect.' . self::USED_TICKETS_QTY_FIELD_KEY . ', 
                    0
                )
            )'
        ;

        $select = $this->getConnection()->select()
            ->from(
                [self::TMP_TABLE_ALIAS => $this->getTable('aw_et_product_sector')],
                [
                    'product_id' => self::TMP_TABLE_ALIAS . '.product_id',
                ]
            )->joinLeft(
                ['sector_table' => $this->getTable('aw_et_sector')],
                'sector_table.id = ' . self::TMP_TABLE_ALIAS . '.sector_id',
                [self::TOTAL_TICKETS_QTY_FIELD_KEY => new \Zend_Db_Expr('SUM(sector_table.tickets_qty)')]
            )->joinLeft(
                ['used_tickets_qty_subselect' => $usedTicketsQtyExpression],
                'used_tickets_qty_subselect.product_id = ' .  self::TMP_TABLE_ALIAS . '.product_id',
                [
                    self::USED_TICKETS_QTY_FIELD_KEY =>
                        'IFNULL(used_tickets_qty_subselect.' . self::USED_TICKETS_QTY_FIELD_KEY . ', 0)',
                    self::AVAILABLE_TICKETS_QTY_FIELD_KEY =>
                        new \Zend_Db_Expr(
                            'IF( '
                                        . $availableTicketsQtyExpression . ' < 0,'
                                        . ' 0, '
                                        . $availableTicketsQtyExpression
                                    . ')'
                        )
                ]
            )->group(self::TMP_TABLE_ALIAS . '.product_id');

        return $select;
    }

    /**
     * Retrieve query for used tickets qty calculation
     *
     * @return Select
     */
    protected function getUsedTicketsQtyQuery()
    {
        $select = $this->getConnection()->select()
            ->from(
                [self::TMP_TABLE_ALIAS => $this->getTable(TicketResourceModel::MAIN_TABLE_NAME)],
                [
                    'product_id' => self::TMP_TABLE_ALIAS . '.product_id',
                    self::USED_TICKETS_QTY_FIELD_KEY => new \Zend_Db_Expr('COUNT(*)'),
                ]
            )->where(
                self::TMP_TABLE_ALIAS . '.status <> ?',
                TicketStatus::CANCELED
            )->group(self::TMP_TABLE_ALIAS . '.product_id');

        return $select;
    }

    /**
     * Attach tickets statistics columns
     *
     * @return Collection
     */
    protected function attachTicketsStatisticsColumns()
    {
        return $this->attachRelationTable(
            $this->getTicketsStatisticsQuery(),
            'entity_id',
            'product_id',
            [
                self::TOTAL_TICKETS_QTY_FIELD_KEY => self::TOTAL_TICKETS_QTY_FIELD_KEY,
                self::USED_TICKETS_QTY_FIELD_KEY => self::USED_TICKETS_QTY_FIELD_KEY,
                self::AVAILABLE_TICKETS_QTY_FIELD_KEY => self::AVAILABLE_TICKETS_QTY_FIELD_KEY,
            ]
        );
    }

    /**
     * Join tickets statistics columns for filtering
     *
     * @return Collection
     */
    protected function joinTicketsStatisticsColumns()
    {
        return $this->joinLinkageTable(
            $this->getTicketsStatisticsQuery(),
            'entity_id',
            'product_id',
            [
                self::TOTAL_TICKETS_QTY_FIELD_KEY => self::TOTAL_TICKETS_QTY_FIELD_KEY,
                self::USED_TICKETS_QTY_FIELD_KEY => self::USED_TICKETS_QTY_FIELD_KEY,
                self::AVAILABLE_TICKETS_QTY_FIELD_KEY => self::AVAILABLE_TICKETS_QTY_FIELD_KEY,
            ],
            'ticket_statistics_subselect'
        );
    }

    /**
     * Attach entity table data to collection items
     * @todo unification of that service logic with Aheadworks\EventTickets\Model\ResourceModel\AbstractCollection
     *
     * @param string|Select $table
     * @param string $columnName
     * @param string $linkageColumnName
     * @param array $attachedFields
     * @param array $conditions
     * @param array $order
     * @return $this
     */
    private function attachRelationTable(
        $table,
        $columnName,
        $linkageColumnName,
        $attachedFields,
        $conditions = [],
        $order = []
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $table instanceof Select
                ? $table
                : $connection->select()->from([self::TMP_TABLE_ALIAS => $this->getTable($table)]);

            $select->where('tmp_table.' . $linkageColumnName . ' IN (?)', $ids);

            foreach ($conditions as $condition) {
                $select->where(
                    self::TMP_TABLE_ALIAS . '.' . $condition['field'] . ' ' . $condition['condition'] . ' (?)',
                    $condition['value']
                );
            }

            if (!empty($order)) {
                $select->order(self::TMP_TABLE_ALIAS . '.' . $order['field'] . ' ' . $order['direction']);
            }
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = [];
                $id = $item->getData($columnName);
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        foreach ($attachedFields as $attachedFieldKey => $columnNameTable) {
                            if (is_array($columnNameTable)) {
                                $fieldValue = [];
                                foreach ($columnNameTable as $columnNameRelation) {
                                    $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                                }
                                $result[$attachedFieldKey] = $fieldValue;
                            } else {
                                $result[$attachedFieldKey] = $data[$columnNameTable];
                            }
                        }
                    }
                }
                if (!empty($result)) {
                    $item->addData($result);
                }
            }
        }
        return $this;
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string|Select $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param array $joinedFields
     * @param string $linkageTableName
     * @return $this
     */
    private function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $joinedFields,
        $linkageTableName
    ) {
        if (!in_array($linkageTableName, $this->linkageTableNames)) {
            $this->linkageTableNames[] = $linkageTableName;
            $table = $tableName instanceof Select
                ? new \Zend_Db_Expr('(' . $tableName . ')')
                : $this->getTable($tableName);

            $this->getSelect()->joinLeft(
                [$linkageTableName => $table],
                self::MAIN_TABLE_ALIAS . '.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );

            foreach ($joinedFields as $columnFilter => $fieldName) {
                $this->addFilterToMap($columnFilter, $linkageTableName . '.' . $fieldName);
            }
        }

        return $this;
    }
}
