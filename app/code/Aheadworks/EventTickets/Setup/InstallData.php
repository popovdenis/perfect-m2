<?php
namespace Aheadworks\EventTickets\Setup;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\EndDate;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\SpaceId;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\TicketSellingDeadlineDate;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\VenueId;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\SpaceList;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\VenueList;
use Aheadworks\EventTickets\Setup\Updater\Data\Updater;
use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime as AttributeDatetime;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\SectorConfiguration as AttributeSectorConfiguration;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\PersonalOptions as AttributePersonalOptions;
use Magento\Framework\Setup\SampleData\Executor as SampleDataExecutor;
use Aheadworks\EventTickets\Setup\SampleData\Installer as SampleDataInstaller;

/**
 * Class InstallData
 *
 * @package Aheadworks\EventTickets\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var Updater
     */
    private $updater;

    /**
     * @param CategorySetupFactory $categorySetupFactory
     * @param Updater $updater
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory,
        Updater $updater
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->createEventTicketOptionAttributes($setup)
            ->createEventTicketPersonalizationAttributes($setup)
            ->createCustomerGroupForTicketManagement($setup);
        $this->updater
            ->update103()
            ->update110($setup)
            ->update120($setup)
            ->update140($setup);
    }

    /**
     * Create event ticket option attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function createEventTicketOptionAttributes(ModuleDataSetupInterface $setup)
    {
        $groupName = 'Event Ticket Options';
        /** @var CategorySetup $installer */
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);
        $installer->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING,
            [
                'group' => $groupName,
                'backend' => '',
                'frontend' => '',
                'type' => 'int',
                'label' => 'Require Shipping',
                'input' => 'boolean',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 10
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_START_DATE,
            [
                'group' => $groupName,
                'backend' => AttributeDatetime::class,
                'frontend' => '',
                'type' => 'datetime',
                'label' => 'Event Start Date',
                'input' => 'date',
                'required' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => true,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 20
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_END_DATE,
            [
                'group' => $groupName,
                'backend' => EndDate::class,
                'frontend' => '',
                'type' => 'datetime',
                'label' => 'Event End Date',
                'input' => 'date',
                'required' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => true,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 30
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_VENUE_ID,
            [
                'group' => $groupName,
                'backend' => VenueId::class,
                'frontend' => '',
                'type' => 'int',
                'label' => 'Event Venue',
                'input' => 'select',
                'required' => true,
                'source' => VenueList::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => true,
                'filterable' => true,
                'is_filterable_in_search' => true,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 40
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_SPACE_ID,
            [
                'group' => $groupName,
                'backend' => SpaceId::class,
                'frontend' => '',
                'type' => 'int',
                'label' => 'Event Space',
                'input' => 'select',
                'required' => true,
                'source' => SpaceList::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 50
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE,
            [
                'group' => $groupName,
                'backend' => '',
                'frontend' => '',
                'type' => 'int',
                'label' => 'Tickets Selling Deadline',
                'input' => 'select',
                'required' => true,
                'source' => TicketSellingDeadline::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 60
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE,
            [
                'group' => $groupName,
                'backend' => TicketSellingDeadlineDate::class,
                'frontend' => '',
                'type' => 'datetime',
                'label' => 'Tickets Selling Deadline Custom Date',
                'input' => 'date',
                'required' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 70
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
            [
                'group' => $groupName,
                'backend' => AttributeSectorConfiguration::class,
                'frontend' => '',
                'type' => 'int',
                'label' => 'Event Sector Configuration',
                'input' => 'select',
                'required' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 80
            ]
        );

        return $this;
    }

    /**
     * Create event ticket personalization attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function createEventTicketPersonalizationAttributes(ModuleDataSetupInterface $setup)
    {
        $groupName = 'Event Ticket Personalization';
        /** @var CategorySetup $installer */
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);
        $installer->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS,
            [
                'group' => $groupName,
                'backend' => AttributePersonalOptions::class,
                'frontend' => '',
                'type' => 'int',
                'label' => 'Options',
                'input' => 'select',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 10
            ]
        );

        return $this;
    }

    /**
     * Create customer group for ticket management on storefront
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function createCustomerGroupForTicketManagement($setup)
    {
        $customerGroupCode = 'AW ET Ticket Management';
        $tableName = 'customer_group';
        $field = 'customer_group_code';
        if (!$this->checkIfExistValue($setup, $tableName, $field, $customerGroupCode)) {
            $setup->getConnection()->insert(
                $setup->getTable($tableName),
                [$field => $customerGroupCode, 'tax_class_id' => 3]
            );
            $customerGroupId = $setup->getConnection()->lastInsertId();
            $this->updateCustomerGroupForTicketManagementValueOnStoreConfig($setup, $customerGroupId);
        }

        return $this;
    }

    /**
     * Update customer group for ticket management value on store config
     *
     * @param ModuleDataSetupInterface $setup
     * @param int $customerGroupId
     */
    private function updateCustomerGroupForTicketManagementValueOnStoreConfig($setup, $customerGroupId)
    {
        $data = [
            'scope' => 'default',
            'scope_id' => 0,
            'path' => Config::XML_PATH_GENERAL_TICKET_MANAGEMENT_GROUP_ON_STOREFRONT,
            'value' => $customerGroupId
        ];

        $setup->getConnection()
            ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);
    }

    /**
     * Check if exist value in table
     *
     * @param ModuleDataSetupInterface $setup
     * @param string $tableName
     * @param string $field
     * @param string $value
     * @return bool
     */
    private function checkIfExistValue($setup, $tableName, $field, $value)
    {
        $connection = $setup->getConnection();
        $select = $connection->select()
            ->from($setup->getTable($tableName), [$field])
            ->where($field . ' = ?', $value)
            ->limit(1);

        return (bool)$connection->fetchOne($select);
    }
}
