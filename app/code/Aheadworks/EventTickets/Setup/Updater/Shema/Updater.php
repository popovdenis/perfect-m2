<?php
namespace Aheadworks\EventTickets\Setup\Updater\Shema;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\RecurringSchedule;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Updater
 * @package Aheadworks\EventTickets\Setup\Updater\Shema
 */
class Updater
{
    /**
     * Update for 1.1.0 version
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    public function update110(SchemaSetupInterface $setup)
    {
        $this
            ->addFieldsToProductOptions($setup)
            ->addFieldsToProductSectorTickets($setup)
            ->addProductSectorTicketsOptionsTable($setup)
            ->addProductSectorProductsTable($setup);
        return $this;
    }

    /**
     * Update for 1.2.0 version
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    public function update120(SchemaSetupInterface $setup)
    {
        $this->addAdditionalPriceFieldsToProductSectorTickets($setup);
        return $this;
    }

    /**
     * Update for 1.4.0 version
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    public function update140(SchemaSetupInterface $setup)
    {
        $this
            ->addRecurringScheduleTable($setup)
            ->addRecurringTimeSlotsTable($setup)
            ->addTimeSlotFieldToTickets($setup)
            ->addIndexToTicketTableColumns($setup);

        return $this;
    }

    /**
     * Update for 1.5.0 version
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    public function update150(SchemaSetupInterface $setup)
    {
        $this->addAdditionalFieldsToRecurringSchedule($setup);

        return $this;
    }

    /**
     * Update for 1.5.6 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update156(SchemaSetupInterface $setup)
    {
        $this
            ->addBaseOriginalPriceToTicketTable($setup)
        ;

        return $this;
    }

    /**
     * Add fields to product options
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addFieldsToProductOptions($setup)
    {
        $tableName = 'aw_et_product_option';
        $this
            ->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => 'apply_to_all_ticket_types',
                        'config' => [
                            'type' => Table::TYPE_SMALLINT,
                            'nullable' => false,
                            'default' => 0,
                            'comment' => 'Apply to All Ticket Types'
                        ]
                    ],
                    [
                        'fieldName' => 'uid',
                        'config' => [
                            'type' => Table::TYPE_TEXT,

                            'nullable' => false,
                            'length' => 50,
                            'after' => 'id',
                            'comment' => 'Uid'
                        ]
                    ]
                ],
                $tableName
            )
            ->addUidDataToTable($setup, $tableName, true);

        return $this;
    }

    /**
     * Add fields to product sector tickets
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addFieldsToProductSectorTickets($setup)
    {
        $tableName = 'aw_et_product_sector_tickets';
        $this
            ->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => 'uid',
                        'config' => [
                            'type' => Table::TYPE_TEXT,

                            'nullable' => false,
                            'length' => 50,
                            'after' => 'product_sector_id',
                            'comment' => 'Uid'
                        ]
                    ]
                ],
                $tableName
            )
            ->addUidDataToTable($setup, $tableName, true);

        return $this;
    }

    /**
     * Add product sector products table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function addProductSectorProductsTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_et_product_sector_products'))
            ->addColumn(
                'product_sector_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Sector ID'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Position'
            )
            ->addForeignKey(
                $setup->getFkName(
                    'aw_et_product_sector_products',
                    'product_sector_id',
                    'aw_et_product_sector',
                    'id'
                ),
                'product_sector_id',
                $setup->getTable('aw_et_product_sector'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'aw_et_product_sector_products',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Product Sector Products Table');
        $setup->getConnection()->createTable($table);
        
        return $this;
    }

    /**
     * Add product sector tickets options table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function addProductSectorTicketsOptionsTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_et_product_sector_tickets_options'))
            ->addColumn(
                'product_sector_ticket_uid',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'primary' => true],
                'Product Sector Ticket Uid'
            )->addColumn(
                'product_option_uid',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'primary' => true],
                'Product Option Uid'
            )->addForeignKey(
                $setup->getFkName(
                    'aw_et_product_sector_tickets_options',
                    'product_sector_ticket_uid',
                    'aw_et_product_sector_tickets',
                    'uid'
                ),
                'product_sector_ticket_uid',
                $setup->getTable('aw_et_product_sector_tickets'),
                'uid',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'aw_et_product_sector_tickets_options',
                    'product_option_uid',
                    'aw_et_product_option',
                    'uid'
                ),
                'product_option_uid',
                $setup->getTable('aw_et_product_option'),
                'uid',
                Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Product Sector Ticket Options Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add columns to table
     *
     * @param SchemaSetupInterface $setup
     * @param array $columnsConfig
     * @param string $tableName
     * @return $this
     */
    private function addColumnsToTable($setup, $columnsConfig, $tableName)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable($tableName);
        foreach ($columnsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['fieldName'];
            if ($connection->tableColumnExists($tableName, $fieldName)) {
                continue;
            }
            $connection->addColumn(
                $tableName,
                $fieldName,
                $fieldConfig['config']
            );
        }

        return $this;
    }

    /**
     * Add columns to table
     *
     * @param SchemaSetupInterface $setup
     * @param string $tableName
     * @param bool $addIndexToUid
     * @return $this
     */
    public function addUidDataToTable($setup, $tableName, $addIndexToUid = false)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable($tableName);

        $select = $connection
            ->select()
            ->from($tableName)
            ->where('uid IS NULL OR uid = ""');
        $rows = $connection->fetchAll($select);
        if (count($rows)) {
            $connection->delete($tableName, 'uid IS NULL OR uid = ""');
            foreach ($rows as &$row) {
                $row['uid'] = uniqid();
            }
            $connection->insertMultiple(
                $tableName,
                $rows
            );
        }

        if ($addIndexToUid) {
            $connection->addIndex(
                $tableName,
                $setup->getIdxName($tableName, ['uid']),
                ['uid'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }
        return $this;
    }

    /**
     * Add additional price fields to product sector tickets
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addAdditionalPriceFieldsToProductSectorTickets($setup)
    {
        $tableName = 'aw_et_product_sector_tickets';
        $this
            ->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => 'early_bird_price',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'nullable' => true,
                            'length' => '12,4',
                            'after' => 'position',
                            'comment' => 'Early Bird Price'
                        ]
                    ]
                ],
                $tableName
            )->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => 'last_days_price',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'nullable' => true,
                            'length' => '12,4',
                            'after' => 'price',
                            'comment' => 'Last Days Price'
                        ]
                    ]
                ],
                $tableName
            );

        return $this;
    }

    /**
     * Add recurring schedule table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function addRecurringScheduleTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(RecurringSchedule::MAIN_TABLE))
            ->addColumn(
                ProductRecurringScheduleInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'ID'
            )->addColumn(
                ProductRecurringScheduleInterface::PRODUCT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                ProductRecurringScheduleInterface::TYPE,
                Table::TYPE_TEXT,
                40,
                ['nullable' => false],
                'Type'
            )->addColumn(
                ProductRecurringScheduleInterface::SELLING_DEADLINE_TYPE,
                Table::TYPE_TEXT,
                40,
                ['nullable' => false],
                'Selling Deadline Type'
            )->addColumn(
                ProductRecurringScheduleInterface::SELLING_DEADLINE_CORRECTION,
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Selling Deadline Correction'
            )->addColumn(
                ProductRecurringScheduleInterface::SCHEDULE_OPTIONS,
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Schedule Options'
            )->addForeignKey(
                $setup->getFkName(
                    RecurringSchedule::MAIN_TABLE,
                    ProductRecurringScheduleInterface::PRODUCT_ID,
                    'catalog_product_entity',
                    'entity_id'
                ),
                ProductRecurringScheduleInterface::PRODUCT_ID,
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Product Recurring Schedule Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add additional price fields to product sector tickets
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addAdditionalFieldsToRecurringSchedule($setup)
    {
        $tableName = RecurringSchedule::MAIN_TABLE;
        $this
            ->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => ProductRecurringScheduleInterface::DAYS_TO_DISPLAY,
                        'config' => [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'after' => ProductRecurringScheduleInterface::SCHEDULE_OPTIONS,
                            'comment' => 'Days To Display'
                        ]
                    ]
                ],
                $tableName
            )->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => ProductRecurringScheduleInterface::FILTER_BY_TICKET_QTY,
                        'config' => [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => false,
                            'size' => 1,
                            'after' => ProductRecurringScheduleInterface::DAYS_TO_DISPLAY,
                            'comment' => 'Enable Filter by Available Ticket Qty'
                        ]
                    ]
                ],
                $tableName
            )->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => ProductRecurringScheduleInterface::MULTISELECTION_TIME_SLOTS,
                        'config' => [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => false,
                            'size' => 1,
                            'after' => ProductRecurringScheduleInterface::FILTER_BY_TICKET_QTY,
                            'comment' => 'Time-slots Multiselection'
                        ]
                    ]
                ],
                $tableName
            );

        return $this;
    }

    /**
     * Add recurring time slots table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function addRecurringTimeSlotsTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(RecurringSchedule::TIME_SLOTS_TABLE_NAME))
            ->addColumn(
                TimeSlotInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'ID'
            )->addColumn(
                TimeSlotInterface::SCHEDULE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Schedule ID'
            )->addColumn(
                TimeSlotInterface::START_TIME,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Start Time'
            )->addColumn(
                TimeSlotInterface::END_TIME,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'End Time'
            )->addForeignKey(
                $setup->getFkName(
                    RecurringSchedule::TIME_SLOTS_TABLE_NAME,
                    TimeSlotInterface::SCHEDULE_ID,
                    RecurringSchedule::MAIN_TABLE,
                    ProductRecurringScheduleInterface::ID
                ),
                TimeSlotInterface::SCHEDULE_ID,
                $setup->getTable(RecurringSchedule::MAIN_TABLE),
                ProductRecurringScheduleInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Product Recurring Time Slots Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add time slot field to tickets table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addTimeSlotFieldToTickets($setup)
    {
        $tableName = $setup->getTable(TicketResourceModel::MAIN_TABLE_NAME);
        $this
            ->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => TicketInterface::RECURRING_TIME_SLOT_ID,
                        'config' => [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'unsigned' => true,
                            'after' => TicketInterface::EVENT_END_DATE,
                            'comment' => 'Recurring Time Slot ID'
                        ]
                    ]
                ],
                $tableName
            );

        return $this;
    }

    /**
     * Add index to ticket table columns
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addIndexToTicketTableColumns($setup)
    {
        $connection = $setup->getConnection();
        $ticketTable = $setup->getTable(TicketResourceModel::MAIN_TABLE_NAME);

        $connection->addIndex(
            $ticketTable,
            $setup->getIdxName($ticketTable, [TicketInterface::STATUS]),
            [TicketInterface::STATUS]
        );
        $connection->addIndex(
            $ticketTable,
            $setup->getIdxName($ticketTable, [TicketInterface::SECTOR_ID, TicketInterface::RECURRING_TIME_SLOT_ID]),
            [TicketInterface::SECTOR_ID, TicketInterface::RECURRING_TIME_SLOT_ID]
        );

        return $this;
    }

    /**
     * Add base original price column to ticket table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addBaseOriginalPriceToTicketTable($setup)
    {
        $tableName = $setup->getTable(TicketResourceModel::MAIN_TABLE_NAME);
        $connection = $setup->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                TicketInterface::BASE_ORIGINAL_PRICE,
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => '0.0000',
                    'length' => '12,4',
                    'comment' => 'Base Original Price',
                ]
            );
        }

        return $this;
    }
}
