<?php
namespace Aheadworks\EventTickets\Model\Product\PersonalOptions\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleReader;

/**
 * Class SchemaLocator
 *
 * @package Aheadworks\EventTickets\Model\Product\PersonalOptions\Config
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var string
     */
    private $schema;

    /**
     * @param ModuleReader $moduleReader
     */
    public function __construct(ModuleReader $moduleReader)
    {
        $this->schema = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Aheadworks_EventTickets')
            . '/product_personal_options.xsd';
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerFileSchema()
    {
        return null;
    }
}
