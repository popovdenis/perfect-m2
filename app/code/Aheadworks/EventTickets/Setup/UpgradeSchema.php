<?php
namespace Aheadworks\EventTickets\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Aheadworks\EventTickets\Setup\Updater\Shema\Updater as SchemaUpdater;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Aheadworks\EventTickets\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
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
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->schemaUpdater->update110($setup);
        }
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->schemaUpdater->update120($setup);
        }
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->schemaUpdater->update140($setup);
        }
        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->schemaUpdater->update150($setup);
        }
        if (version_compare($context->getVersion(), '1.5.6', '<')) {
            $this->schemaUpdater->update156($setup);
        }

        $setup->endSetup();
    }
}
