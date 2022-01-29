<?php
namespace Aheadworks\EventTickets\Setup;

use Aheadworks\EventTickets\Setup\Updater\Data\Updater;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Aheadworks\EventTickets\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Updater
     */
    private $updater;

    /**
     * @param Updater $updater
     */
    public function __construct(
        Updater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->updater->update103();
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->updater->update110($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->updater->update120($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->updater->update140($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->updater->update150($setup);
        }
        $setup->endSetup();
    }
}
