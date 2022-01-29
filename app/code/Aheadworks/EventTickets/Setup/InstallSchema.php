<?php
namespace Aheadworks\EventTickets\Setup;

use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as DataDefinition;
use Aheadworks\EventTickets\Setup\Updater\Shema\Updater as SchemaUpdater;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\EventTickets\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var SchemaUpdater
     */
    private $schemaUpdater;

    /**
     * @param SchemaUpdater $schemaUpdater
     */
    public function __construct(
        SchemaUpdater $schemaUpdater
    ) {
        $this->schemaUpdater = $schemaUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createVenueTable($installer)
            ->createSpaceTable($installer)
            ->createSectorTable($installer)
            ->createTicketTable($installer)
            ->createTicketOptionTable($installer)
            ->createTicketTypeTable($installer)
            ->createProductSectorTables($installer)
            ->createProductOptionTables($installer)
            ->createLabelTable($setup);

        $this->schemaUpdater
            ->update110($setup)
            ->update120($setup)
            ->update140($setup)
            ->update150($setup)
            ->update156($setup)
        ;

        $installer->endSetup();
    }

    /**
     * Create table 'aw_et_venue'
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createVenueTable(SchemaSetupInterface $installer)
    {
        $venueTable = $installer->getConnection()->newTable($installer->getTable('aw_et_venue'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Venue ID'
            )
            ->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Venue Status'
            )
            ->addColumn(
                'name',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Venue Name'
            )
            ->addColumn(
                'address',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Address'
            )
            ->addColumn(
                'coordinates',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Coordinates'
            )
            ->addColumn(
                'image_path',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Venue Image path'
            )->setComment('AW Event Tickets Venue Table');
        $installer->getConnection()->createTable($venueTable);
        return $this;
    }

    /**
     * Create table 'aw_et_space'
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createSpaceTable(SchemaSetupInterface $installer)
    {
        $spaceTable = $installer->getConnection()->newTable($installer->getTable('aw_et_space'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Space ID'
            )
            ->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Space Status'
            )
            ->addColumn(
                'name',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Space Name'
            )
            ->addColumn(
                'venue_id',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Venue ID'
            )
            ->addColumn(
                'tickets_qty',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Space Tickets Quantity'
            )
            ->addColumn(
                'image_path',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Space Image path'
            )->addIndex(
                $installer->getIdxName('aw_et_space', ['venue_id']),
                ['venue_id']
            )->setComment('AW Event Tickets Space Table');
        $installer->getConnection()->createTable($spaceTable);
        return $this;
    }

    /**
     * Create table 'aw_et_sector'
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createSectorTable(SchemaSetupInterface $installer)
    {
        $sectorTable = $installer->getConnection()->newTable($installer->getTable('aw_et_sector'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Sector ID'
            )
            ->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sector Status'
            )
            ->addColumn(
                'name',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Sector Name'
            )
            ->addColumn(
                'sku',
                DataDefinition::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Sku'
            )
            ->addColumn(
                'tickets_qty',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Sector Tickets Quantity'
            )
            ->addColumn(
                'image_path',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Space Image path'
            )
            ->addColumn(
                'space_id',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Space ID'
            )
            ->addColumn(
                'sort_order',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Sector Sort Order'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_sector', ['space_id']),
                ['space_id']
            )->addForeignKey(
                $installer->getFkName(
                    'aw_et_sector',
                    'space_id',
                    'aw_et_space',
                    'id'
                ),
                'space_id',
                $installer->getTable('aw_et_space'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Sector Table');
        $installer->getConnection()->createTable($sectorTable);
        return $this;
    }

    /**
     * Create table 'aw_et_ticket'
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createTicketTable(SchemaSetupInterface $installer)
    {
        $sectorTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(TicketResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Ticket ID'
            )
            ->addColumn(
                'order_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true
                ],
                'Order ID'
            )
            ->addColumn(
                'product_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true
                ],
                'Product ID'
            )
            ->addColumn(
                'store_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Store ID'
            )
            ->addColumn(
                'number',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Ticket Number'
            )
            ->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ticket Status'
            )
            ->addColumn(
                'email_sent',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email Sent'
            )
            ->addColumn(
                'ticket_type_id',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ticket Type ID'
            )
            ->addColumn(
                'venue_id',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Venue ID'
            )
            ->addColumn(
                'sector_id',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sector ID'
            )
            ->addColumn(
                'customer_id',
                DataDefinition::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Customer ID'
            )
            ->addColumn(
                'customer_name',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Customer Name'
            )
            ->addColumn(
                'customer_email',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Customer Email'
            )
            ->addColumn(
                'base_price',
                DataDefinition::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Base Price'
            )
            ->addColumn(
                'sector_storefront_title',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Sector Storefront Title'
            )
            ->addColumn(
                'ticket_type_storefront_title',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Ticket Type Storefront Title'
            )
            ->addColumn(
                'event_title',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Event Title'
            )
            ->addColumn(
                'event_address',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Event Address'
            )
            ->addColumn(
                'event_description',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Event Description'
            )
            ->addColumn(
                'event_image',
                DataDefinition::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Event Image'
            )
            ->addColumn(
                'event_start_date',
                DataDefinition::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Event Start Date'
            )
            ->addColumn(
                'event_end_date',
                DataDefinition::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Event End Date'
            )
            ->addIndex(
                $installer->getIdxName(TicketResourceModel::MAIN_TABLE_NAME, ['id', 'number']),
                ['id', 'number']
            )->setComment('AW Event Tickets Ticket Table');
        $installer->getConnection()->createTable($sectorTable);
        return $this;
    }

    /**
     * Create table 'aw_et_ticket_type'
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createTicketOptionTable(SchemaSetupInterface $installer)
    {
        $ticketOptionTable = $installer->getConnection()->newTable($installer->getTable('aw_et_ticket_option'))
            ->addColumn(
                'ticket_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Ticket ID'
            )
            ->addColumn(
                'name',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Option Name'
            )
            ->addColumn(
                'type',
                DataDefinition::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Option Type'
            )
            ->addColumn(
                'value',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Option Value'
            )
            ->addColumn(
                'key',
                DataDefinition::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Option Key'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_ticket_option', ['ticket_id']),
                ['ticket_id']
            )
            ->addIndex(
                $installer->getIdxName('aw_et_ticket_option', ['name']),
                ['name']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_et_ticket_option',
                    'ticket_id',
                    TicketResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'ticket_id',
                $installer->getTable(TicketResourceModel::MAIN_TABLE_NAME),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Ticket Option Table');
        $installer->getConnection()->createTable($ticketOptionTable);
        return $this;
    }

    /**
     * Create table 'aw_et_ticket_type'
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createTicketTypeTable(SchemaSetupInterface $installer)
    {
        $ticketTypeTable = $installer->getConnection()->newTable($installer->getTable('aw_et_ticket_type'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Ticket Type ID'
            )
            ->addColumn(
                'name',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Ticket Type Name'
            )
            ->addColumn(
                'sku',
                DataDefinition::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Sku'
            )
            ->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ticket Type Status'
            )->setComment('AW Event Tickets Ticket Type Table');
        $installer->getConnection()->createTable($ticketTypeTable);
        return $this;
    }

    /**
     * Create 'aw_et_product_sector' and 'aw_et_product_sector_tickets' tables
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createProductSectorTables(SchemaSetupInterface $installer)
    {
        $productSectorTable = $installer->getConnection()->newTable($installer->getTable('aw_et_product_sector'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'identity' => true
                ],
                'Id'
            )
            ->addColumn(
                'product_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Id'
            )
            ->addColumn(
                'sector_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Sector Id'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_product_sector', ['product_id']),
                ['product_id']
            )
            ->addIndex(
                $installer->getIdxName('aw_et_product_sector', ['sector_id']),
                ['sector_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_et_product_sector',
                    'sector_id',
                    'aw_et_sector',
                    'id'
                ),
                'sector_id',
                $installer->getTable('aw_et_sector'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Sector To Product Relation Table');
        $installer->getConnection()->createTable($productSectorTable);

        $productSectorTicketsTable = $installer->getConnection()
            ->newTable($installer->getTable('aw_et_product_sector_tickets'))
            ->addColumn(
                'product_sector_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Sector Id'
            )
            ->addColumn(
                'type_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Type Id'
            )
            ->addColumn(
                'position',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Position'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Price'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_product_sector_tickets', ['type_id']),
                ['type_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_et_product_sector_tickets',
                    'product_sector_id',
                    'aw_et_product_sector',
                    'id'
                ),
                'product_sector_id',
                $installer->getTable('aw_et_product_sector'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_et_product_sector_tickets',
                    'type_id',
                    'aw_et_ticket_type',
                    'id'
                ),
                'type_id',
                $installer->getTable('aw_et_ticket_type'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Sector To Product Relation Table');
        $installer->getConnection()->createTable($productSectorTicketsTable);
        return $this;
    }

    /**
     * Create 'aw_et_product_option' and 'aw_et_product_option_type_value' tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createProductOptionTables(SchemaSetupInterface $installer)
    {
        $productOptionTable = $installer->getConnection()->newTable($installer->getTable('aw_et_product_option'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'identity' => true
                ],
                'Id'
            )
            ->addColumn(
                'product_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Id'
            )
            ->addColumn(
                'type',
                DataDefinition::TYPE_TEXT,
                80,
                [
                    'nullable' => false
                ],
                'Type'
            )
            ->addColumn(
                'require',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Is Require'
            )
            ->addColumn(
                'sort_order',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Sort Order'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_product_option', ['product_id']),
                ['product_id']
            )
            ->setComment('AW Event Tickets Option To Product Relation Table');
        $installer->getConnection()->createTable($productOptionTable);

        $productOptionTypeValueTable = $installer->getConnection()
            ->newTable($installer->getTable('aw_et_product_option_type_value'))
            ->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'identity' => true
                ],
                'Id'
            )
            ->addColumn(
                'option_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Id'
            )
            ->addColumn(
                'sort_order',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Sort Order'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_product_option_type_value', ['option_id']),
                ['option_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_et_product_option_type_value',
                    'option_id',
                    'aw_et_product_option',
                    'id'
                ),
                'option_id',
                $installer->getTable('aw_et_product_option'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Type Value To Option Relation Table');
        $installer->getConnection()->createTable($productOptionTypeValueTable);

        return $this;
    }

    /**
     * Create table 'aw_et_label'
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     * @return $this
     */
    private function createLabelTable(SchemaSetupInterface $installer)
    {
        $labelTable = $installer->getConnection()->newTable($installer->getTable('aw_et_label'))
            ->addColumn(
                'store_id',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store Id'
            )
            ->addColumn(
                'entity_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity Id'
            )
            ->addColumn(
                'entity_type',
                DataDefinition::TYPE_TEXT,
                100,
                [
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity Type'
            )
            ->addColumn(
                'title',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Entity Title'
            )
            ->addColumn(
                'description',
                DataDefinition::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Entity Description'
            )
            ->addIndex(
                $installer->getIdxName('aw_et_label', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_et_label',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('AW Event Tickets Entity Label To Store Relation Table');
        $installer->getConnection()->createTable($labelTable);
        return $this;
    }
}
