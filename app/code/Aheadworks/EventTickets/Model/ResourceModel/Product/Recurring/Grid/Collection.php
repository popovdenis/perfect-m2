<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Recurring\Grid;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket as EventTicketProductType;
use Aheadworks\EventTickets\Model\ResourceModel\Product\RecurringSchedule;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DB\Select;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * Class Collection
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Recurring\Grid
 */
class Collection extends ProductCollection
{
    /**
     * Keys for additional data of collection items
     */
    const EVENT_DATE_FIELD_KEY = 'event_date';
    const TMP_ID_FIELD_KEY = 'tmp_id';
    const ENTITY_ID_KEY = 'entity_id';
    const TIME_SLOT_FIELD_KEY = 'time_slot';

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            [self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]
        )->joinCross(
            ['dummy' => $this->getConnection()->select()->from('', new \Zend_Db_Expr('@i:=0'))]
        )->joinInner(
            [self::TMP_TABLE_ALIAS => $this->getFragmentationSelect()],
            self::TMP_TABLE_ALIAS. '.' . TicketInterface::PRODUCT_ID . '=' . self::MAIN_TABLE_ALIAS. '.entity_id',
            [
                self::TMP_ID_FIELD_KEY => new \Zend_Db_Expr('@i:=@i+1'),
                self::EVENT_DATE_FIELD_KEY,
                TicketInterface::RECURRING_TIME_SLOT_ID,
                TimeSlotInterface::START_TIME,
                TimeSlotInterface::END_TIME
            ]
        )->group([
            self::TMP_TABLE_ALIAS . '.' . self::EVENT_DATE_FIELD_KEY,
            self::TMP_TABLE_ALIAS . '.' . TicketInterface::RECURRING_TIME_SLOT_ID
        ]);
        $this
            ->addFieldToFilter(ProductInterface::TYPE_ID, ['eq' => EventTicketProductType::TYPE_CODE])
            ->addAttributeToFilter(ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE, ScheduleType::RECURRING);
    }

    /**
     * @inheritDoc
     */
    protected function getTicketsStatisticsQuery()
    {
        $usedTicketsQtyExpression = new \Zend_Db_Expr('(' . $this->getUsedTicketsQtyQuery(). ')');

        $availableTicketsQtyExpression = new \Zend_Db_Expr(
            '(
                        SUM(sector_table.tickets_qty) - ' .
                        'IFNULL(
                            used_tickets_qty_subselect.' . self::USED_TICKETS_QTY_FIELD_KEY . ',
                            0
                        )
                      )'
        );

        $select = $this->getConnection()->select()
            ->from(
                [self::TMP_TABLE_ALIAS => $this->getTable('aw_et_product_sector')],
                [
                    'product_id' => self::TMP_TABLE_ALIAS . '.product_id'
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
                        ),
                    self::EVENT_DATE_FIELD_KEY,
                    TicketInterface::RECURRING_TIME_SLOT_ID
                ]
            )->group([
                self::TMP_TABLE_ALIAS . '.product_id',
                TicketInterface::RECURRING_TIME_SLOT_ID,
                self::EVENT_DATE_FIELD_KEY
            ]);

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function getUsedTicketsQtyQuery()
    {
        $eventDateExpr = new \Zend_Db_Expr('DATE(' . TicketInterface::EVENT_START_DATE . ')');
        $select = $this->getConnection()->select()
            ->from(
                [self::TMP_TABLE_ALIAS => $this->getTable(TicketResourceModel::MAIN_TABLE_NAME)],
                [
                    'product_id' => self::TMP_TABLE_ALIAS . '.product_id',
                    self::USED_TICKETS_QTY_FIELD_KEY => new \Zend_Db_Expr('COUNT(*)'),
                    self::EVENT_DATE_FIELD_KEY => $eventDateExpr,
                    TicketInterface::RECURRING_TIME_SLOT_ID
                ]
            )->where(
                self::TMP_TABLE_ALIAS . '.status <> ?',
                TicketStatus::CANCELED
            )->group([
                self::TMP_TABLE_ALIAS . '.product_id',
                TicketInterface::RECURRING_TIME_SLOT_ID,
                self::EVENT_DATE_FIELD_KEY
            ]);

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->_setIdFieldName(self::TMP_ID_FIELD_KEY);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _getSelectCountSql(?Select $select = null, $resetLeftJoins = true)
    {
        $countSelect = parent::_getSelectCountSql($select, false);

        return $this->getConnection()->select()->from(
            $countSelect,
            [new \Zend_Db_Expr('COUNT(*)')]
        );
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (is_string($field) && $field == self::EVENT_DATE_FIELD_KEY) {
            return $this->addQueryFilter(TicketInterface::EVENT_START_DATE, $condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @inheritDoc
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute == self::EVENT_DATE_FIELD_KEY) {
            $this->getSelect()->order($attribute . ' ' . $dir);
            return $this;
        } elseif ($attribute == self::TIME_SLOT_FIELD_KEY) {
            $this->getSelect()->order(TimeSlotInterface::START_TIME . ' ' . $dir);
            return $this;
        } else {
            return parent::addAttributeToSort($attribute, $dir);
        }
    }

    /**
     * @inheritDoc
     */
    protected function attachTicketsStatisticsColumns()
    {
        $ids = $this->getColumnValues(self::ENTITY_ID_KEY);

        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $this->getTicketsStatisticsQuery();

            $select->where('tmp_table.' . TicketInterface::PRODUCT_ID . ' IN (?)', $ids);

            foreach ($this as $item) {
                $result = [];
                $attachedFields = [
                    self::TOTAL_TICKETS_QTY_FIELD_KEY,
                    self::USED_TICKETS_QTY_FIELD_KEY,
                    self::AVAILABLE_TICKETS_QTY_FIELD_KEY
                ];

                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[TicketInterface::PRODUCT_ID] == $item->getData(self::ENTITY_ID_KEY)
                        && $data[self::EVENT_DATE_FIELD_KEY] == $item->getData(self::EVENT_DATE_FIELD_KEY)
                        && $data[TicketInterface::RECURRING_TIME_SLOT_ID]
                        == $item->getData(TicketInterface::RECURRING_TIME_SLOT_ID)
                    ) {
                        foreach ($attachedFields as $fieldName) {
                            $result[$fieldName] = $data[$fieldName];
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
     * @inheritDoc
     */
    protected function joinTicketsStatisticsColumns()
    {
        $alias = 'ticket_statistics_subselect';
        if (!in_array($alias, $this->linkageTableNames)) {
            $this->linkageTableNames[] = $alias;

            $this->getSelect()->joinLeft(
                [$alias => $this->getTicketsStatisticsQuery()],
                self::MAIN_TABLE_ALIAS . '.entity_id = ' . $alias . '.product_id AND '
                . self::TMP_TABLE_ALIAS . '.' . TicketInterface::RECURRING_TIME_SLOT_ID . ' = ' . $alias
                . '.' . TicketInterface::RECURRING_TIME_SLOT_ID . ' AND '
                . self::TMP_TABLE_ALIAS . '.' . self::EVENT_DATE_FIELD_KEY . ' = ' . $alias
                . '.' . self::EVENT_DATE_FIELD_KEY,
                []
            );

            foreach ($this->ticketsStatisticsFields as $fieldName) {
                $this->addFilterToMap($fieldName, $alias . '.' . $fieldName);
            }
        }

        return $this;
    }

    /**
     * Get fragmentation select query
     *
     * @return Select
     */
    private function getFragmentationSelect()
    {
        $eventDateExpr = new \Zend_Db_Expr('DATE(' . TicketInterface::EVENT_START_DATE . ')');
        $select = $this->getConnection()->select()->from(
            [self::TMP_TABLE_ALIAS => $this->getTable(TicketResourceModel::MAIN_TABLE_NAME)],
            [
                self::EVENT_DATE_FIELD_KEY => $eventDateExpr,
                TicketInterface::RECURRING_TIME_SLOT_ID,
                TicketInterface::PRODUCT_ID,
                TicketInterface::EVENT_START_DATE
            ]
        )->joinInner(
            ['time_slot' => $this->getTable(RecurringSchedule::TIME_SLOTS_TABLE_NAME)],
            'time_slot.id = ' . self::TMP_TABLE_ALIAS . '.' . TicketInterface::RECURRING_TIME_SLOT_ID,
            [
                TimeSlotInterface::START_TIME,
                TimeSlotInterface::END_TIME
            ]
        )->group([
            self::TMP_TABLE_ALIAS . '.' . TicketInterface::PRODUCT_ID,
            self::TMP_TABLE_ALIAS . '.' . TicketInterface::RECURRING_TIME_SLOT_ID,
            self::EVENT_DATE_FIELD_KEY
        ]);

        return $select;
    }

    /**
     * Add full name filter
     *
     * @param string $field
     * @param null|string|array $condition
     * @return $this
     */
    private function addQueryFilter($field, $condition)
    {
        $connection = $this->getConnection();
        $sqlCondition = $connection->prepareSqlCondition($field, $condition);

        $this->getSelect()->where($sqlCondition);

        return $this;
    }
}
