<?php

namespace Perfect\Event\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\GroupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class CustomerAttribute
 *
 * @package Perfect\Event\Setup\Patch\Data
 */
class CustomerAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var \Magento\Customer\Setup\CustomerSetup
     */
    private $customerSetup;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute
     */
    private $attributeResource;

    /**
     * @param ModuleDataSetupInterface                        $moduleDataSetup
     * @param \Magento\Eav\Setup\EavSetupFactory              $eavSetupFactory
     * @param \Magento\Eav\Model\Config                       $eavConfig
     * @param \Magento\Customer\Setup\CustomerSetupFactory    $customerSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory  $attributeSetFactory
     * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeResource = $attributeResource;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerAttributes = [
            'phone' => [
                'type' => 'varchar',
                'label' => 'Phone',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 85,
                'system' => 0
            ],
            'job_position' => [
                'type' => 'int',
                'label' => 'Job position',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'required' => true,
                'visible' => true,
                'user_defined' => true,
                'position' => 80,
                'system' => 0
            ],
            'skill_level' => [
                'type' => 'int',
                'label' => 'Skill level',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'required' => true,
                'visible' => true,
                'user_defined' => true,
                'position' => 81,
                'system' => 0
            ]
        ];
        foreach ($customerAttributes as $attributeCode => $attributeData) {
            $eavSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeData);
            $this->createCustomerAttribute(
                $attributeCode,
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
        }


        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @param string $attributeCode
     * @param array  $customerForms
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createCustomerAttribute(string $attributeCode, array $customerForms)
    {
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $customerEntity = $this->customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attribute->setData('used_in_forms', $customerForms);
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId
        ]);
        $this->attributeResource->save($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.3';
    }
}