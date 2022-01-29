<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket\Grid;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Collection as TicketCollection;
use Aheadworks\EventTickets\Model\Ticket\Option\Resolver as TicketOptionResolver;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket\Grid
 */
class Collection extends TicketCollection implements SearchResultInterface
{
    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'slot_id' => TicketInterface::RECURRING_TIME_SLOT_ID
        ]
    ];

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param TicketOptionResolver $ticketOptionResolver
     * @param mixed|null $mainTable
     * @param AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param mixed $resourceModel
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        TicketOptionResolver $ticketOptionResolver,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = Document::class,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $ticketOptionResolver,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->prepareCustomTicketOptionsData();

        return $this;
    }

    /**
     * Prepare custom ticket options data for rendering in the grid
     *
     * @return $this
     */
    private function prepareCustomTicketOptionsData()
    {
        /** @var Document $item */
        foreach ($this as $item) {
            $ticketOptionsData = $item->getData(TicketInterface::OPTIONS);
            if (!isset($ticketOptionsData) || !is_array($ticketOptionsData)) {
                continue;
            }
            foreach ($ticketOptionsData as $ticketOptionDataRow) {
                $fieldName = $ticketOptionDataRow[TicketOptionInterface::KEY];
                $item->setData($fieldName, $ticketOptionDataRow[TicketOptionInterface::VALUE]);
            }
        }
        return $this;
    }
}
