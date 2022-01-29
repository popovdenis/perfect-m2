<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Aheadworks\EventTickets\Model\ResourceModel\AbstractCollection;
use Aheadworks\EventTickets\Model\Ticket;
use Aheadworks\EventTickets\Model\Ticket\Option\Resolver as TicketOptionResolver;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as ResourceTicket;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket
 */
class Collection extends AbstractCollection
{
    /**#@+
     * Constants defined for additional grid columns
     */
    const ORDER_INCREMENT_ID_COLUMN_NAME = 'order_increment_id';
    /**#@-*/

    /**
     * @var string
     */
    protected $_idFieldName = TicketInterface::ID;

    /**
     * @var TicketOptionResolver
     */
    protected $ticketOptionResolver;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param TicketOptionResolver $ticketOptionResolver
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        TicketOptionResolver $ticketOptionResolver,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->ticketOptionResolver = $ticketOptionResolver;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Ticket::class, ResourceTicket::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachOrderIncrementIdColumn();
        $this->attachOptions();
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::addOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function unshiftOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::unshiftOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = $this->processAddFieldToFilter($field, $condition);

        if (!empty($fieldsToProcess)) {
            return parent::addFieldToFilter($fieldsToProcess, $condition);
        }

        return $this;
    }

    /**
     * Process adding fields to filter
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|string
     */
    private function processAddFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = null;
        if (is_array($field)) {
            $fieldsToProcess = [];
            foreach ($field as $fieldName) {
                if ($this->isNeedToApplyPublicFilterToField($fieldName)) {
                    $this->addFilter($fieldName, $condition, 'public');
                } else {
                    $fieldsToProcess[] = $fieldName;
                }
            }
        } else {
            if ($this->isNeedToApplyPublicFilterToField($field)) {
                $this->addFilter($field, $condition, 'public');
            } else {
                $fieldsToProcess = $field;
            }
        }

        return $fieldsToProcess;
    }

    /**
     * Check if need to apply public filter instead of native logic
     *
     * @param string $fieldName
     * @return bool
     */
    private function isNeedToApplyPublicFilterToField($fieldName)
    {
        return (in_array($fieldName, [self::ORDER_INCREMENT_ID_COLUMN_NAME])
            || $this->ticketOptionResolver->checkIfFieldIsTicketOption($fieldName));
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter(self::ORDER_INCREMENT_ID_COLUMN_NAME)) {
            $this->joinOrderIncrementIdColumn();
        }
        $this->joinTicketOptionsForCurrentFilters();
        parent::_renderFiltersBefore();
    }

    /**
     * Join fields for sort order
     *
     * @param string $field
     * @return $this
     */
    private function joinFieldsForSortOrder($field)
    {
        if ($field == self::ORDER_INCREMENT_ID_COLUMN_NAME) {
            $this->joinOrderIncrementIdColumn();
        }
        if ($this->ticketOptionResolver->checkIfFieldIsTicketOption($field)) {
            $this->joinTicketOption($field);
        }
        return $this;
    }

    /**
     * Attach order increment id column
     *
     * @return $this
     */
    private function attachOrderIncrementIdColumn()
    {
        return $this->attachRelationTable(
            'sales_order',
            TicketInterface::ORDER_ID,
            OrderInterface::ENTITY_ID,
            OrderInterface::INCREMENT_ID,
            self::ORDER_INCREMENT_ID_COLUMN_NAME
        );
    }

    /**
     * Join order increment id for filtering
     *
     * @return $this
     */
    private function joinOrderIncrementIdColumn()
    {
        return $this->joinLinkageTable(
            'sales_order',
            TicketInterface::ORDER_ID,
            OrderInterface::ENTITY_ID,
            self::ORDER_INCREMENT_ID_COLUMN_NAME,
            OrderInterface::INCREMENT_ID
        );
    }

    /**
     * Attach all custom options for tickets
     *
     * @return $this
     */
    private function attachOptions()
    {
        return $this->attachRelationTable(
            'aw_et_ticket_option',
            TicketInterface::ID,
            'ticket_id',
            [
                TicketOptionInterface::NAME,
                TicketOptionInterface::TYPE,
                TicketOptionInterface::VALUE,
                TicketOptionInterface::KEY
            ],
            TicketInterface::OPTIONS,
            [],
            [],
            true
        );
    }

    private function joinTicketOptionsForCurrentFilters()
    {
        foreach ($this->_filters as $filter) {
            if ($this->ticketOptionResolver->checkIfFieldIsTicketOption($filter['field'])) {
                $this->joinTicketOption($filter['field']);
            }
        }
        return $this;
    }

    private function joinTicketOption($ticketOptionFieldName)
    {
        return $this->joinLinkageTable(
            'aw_et_ticket_option',
            TicketInterface::ID,
            'ticket_id',
            $ticketOptionFieldName,
            TicketOptionInterface::VALUE,
            [
                [
                    'field' => TicketOptionInterface::KEY,
                    'condition' => '=',
                    'value' => $ticketOptionFieldName
                ]
            ]
        );
    }
}
