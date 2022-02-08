<?php

namespace Perfect\Event\Setup\Patch\Data;

use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;

/**
 * Class CustomerGroup
 *
 * @package Perfect\Event\Setup\Patch\Data
 */
class CustomerGroup implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * @param ModuleDataSetupInterface             $moduleDataSetup
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        GroupFactory $groupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->groupFactory = $groupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        foreach (['Сотрудник', 'Клиент'] as $customerGroup) {
            $group = $this->groupFactory->create();
            try {
                $group->setCode($customerGroup)
                    ->setTaxClassId(GroupRepository::DEFAULT_TAX_CLASS_ID)
                    ->save();
            } catch (\Exception $e) {
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
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
        return '1.0.1';
    }
}