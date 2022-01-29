<?php
namespace Aheadworks\EventTickets\Setup\Updater\Data;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\StartDate;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\EventTickets\Model\Source\Product\AllowedType;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\EarlyBirdEndDate;
use Aheadworks\EventTickets\Model\Product\Attribute\Backend\LastDaysStartDate;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\ScheduleType as ScheduleTypeSource;

/**
 * Class Updater
 * @package Aheadworks\EventTickets\Setup\Updater\Data
 */
class Updater
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var AllowedType
     */
    private $allowedType;

    /**
     * @param EavSetup $eavSetup
     * @param CategorySetupFactory $categorySetupFactory
     * @param AllowedType $allowedType
     */
    public function __construct(
        EavSetup $eavSetup,
        CategorySetupFactory $categorySetupFactory,
        AllowedType $allowedType
    ) {
        $this->eavSetup = $eavSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->allowedType = $allowedType;
    }

    /**
     * Update for 1.0.3 version
     *
     * @return $this
     */
    public function update103()
    {
        $this->updateCatalogAttributes(['weight', 'tax_class_id']);
        return $this;
    }

    /**
     * Update for 1.1.0 version
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    public function update110(ModuleDataSetupInterface $setup)
    {
        $this->addEventTicketRelatedOptions($setup);
        return $this;
    }

    /**
     * Update for 1.2.0 version
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    public function update120(ModuleDataSetupInterface $setup)
    {
        $this->updateRequireShippingEventTicketAttribute();
        $this->addPriceTimeRelatedDateAttributes($setup);
        $this->removeSpaceFromExclusiveProductAttrCode($setup);
        return $this;
    }

    /**
     * Update for 1.4.0 version
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    public function update140(ModuleDataSetupInterface $setup)
    {
        $this
            ->addScheduleAttributes($setup)
            ->modifyEventStartDate($setup);

        return $this;
    }

    /**
     * Update for 1.5.0 version
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    public function update150(ModuleDataSetupInterface $setup)
    {
        $this->modifyTicketNumber($setup);

        return $this;
    }

    /**
     * Update catalog attributes
     *
     * @param array $fieldListToUpdate
     * @return $this
     */
    private function updateCatalogAttributes($fieldListToUpdate)
    {
        foreach ($fieldListToUpdate as $field) {
            $applyTo = explode(
                ',',
                $this->eavSetup->getAttribute(Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array(EventTicket::TYPE_CODE, $applyTo)) {
                $applyTo[] = EventTicket::TYPE_CODE;
                $this->eavSetup->updateAttribute(
                    Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }
        return $this;
    }

    /**
     * Add Event Ticket related options
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function addEventTicketRelatedOptions($setup)
    {
        $groupName = 'Event Ticket Related Options';
        $productTypes = $this->allowedType->getTypeList();
        $productTypes = join(',', $productTypes);

        /** @var CategorySetup $installer */
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);
        $installer->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_EXCLUSIVE_PRODUCT,
            [
                'group' => $groupName,
                'backend' => '',
                'frontend' => '',
                'type' => 'int',
                'label' => 'Can be Purchased Only With a Ticket',
                'input' => 'boolean',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'apply_to' => $productTypes,
                'sort_order' => 10
            ]
        );
        return $this;
    }

    /**
     * Update require shipping attribute for event ticket products
     *
     * @return $this
     */
    private function updateRequireShippingEventTicketAttribute()
    {
        $this->eavSetup->updateAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING,
            'source_model',
            \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class
        );
        return $this;
    }

    /**
     * Add Event Ticket price time related date attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function addPriceTimeRelatedDateAttributes($setup)
    {
        $groupName = 'Event Ticket Options';

        /** @var CategorySetup $installer */
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);
        $installer->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE,
            [
                'group' => $groupName,
                'backend' => EarlyBirdEndDate::class,
                'frontend' => '',
                'type' => 'datetime',
                'label' => 'Early Bird Tickets Sale End Date',
                'input' => 'date',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => true,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 71
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE,
            [
                'group' => $groupName,
                'backend' => LastDaysStartDate::class,
                'frontend' => '',
                'type' => 'datetime',
                'label' => 'Last Day(s) Price Start Date',
                'input' => 'date',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => true,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 72
            ]
        );
        return $this;
    }

    /**
     * Remove space from exclusive product attribute code
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function removeSpaceFromExclusiveProductAttrCode($setup)
    {
        $connection = $setup->getConnection();
        $connection->update(
            $setup->getTable('eav_attribute'),
            [
                'attribute_code' => ProductAttributeInterface::CODE_AW_ET_EXCLUSIVE_PRODUCT,
            ],
            $connection->quoteInto('attribute_code = ?', ProductAttributeInterface::CODE_AW_ET_EXCLUSIVE_PRODUCT . ' ')
        );

        return $this;
    }

    /**
     * Add Event Ticket schedule attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function addScheduleAttributes($setup)
    {
        $groupName = 'Event Ticket Options';

        /** @var CategorySetup $installer */
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);
        $installer->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE,
            [
                'group' => $groupName,
                'backend' => '',
                'frontend' => '',
                'type' => 'int',
                'label' => 'Schedule Type',
                'input' => 'select',
                'required' => true,
                'source' => ScheduleType::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'default' => ScheduleType::ONE_TIME,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 15
            ]
        )->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE_TYPE,
            [
                'group' => $groupName,
                'backend' => '',
                'frontend' => '',
                'type' => 'varchar',
                'label' => 'Recurring Schedule Type',
                'input' => 'select',
                'required' => true,
                'source' => ScheduleTypeSource::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'default' => ScheduleTypeSource::DAILY,
                'apply_to' => EventTicket::TYPE_CODE,
                'sort_order' => 16
            ]
        );

        return $this;
    }

    /**
     * Modify Event Ticket start date
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function modifyEventStartDate($setup)
    {
        /** @var CategorySetup $installer */
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);
        $installer->updateAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_ET_START_DATE,
            'backend_model',
            StartDate::class
        );

        return $this;
    }

    /**
     * Modify Ticket number
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function modifyTicketNumber($setup)
    {
        $connection = $setup->getConnection();
        $connection->update(
            $setup->getTable(TicketResourceModel::MAIN_TABLE_NAME),
            [
                TicketInterface::NUMBER => new \Zend_Db_Expr('UPPER(' . TicketInterface::NUMBER . ')'),
            ]
        );

        return $this;
    }
}
