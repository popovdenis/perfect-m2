<?php
// @codingStandardsIgnoreStart
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable;

use Magento\Catalog\Model\Product;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable
 */
class Factory
{
    // @codingStandardsIgnoreEnd
    /**
     * Swatches module Name
     */
    const SWATCHES_MODULE_NAME = 'Magento_Swatches';

    /**
     * Configurable Product module Name
     */
    const CONFIGURABLE_PRODUCT_MODULE_NAME = 'Magento_ConfigurableProduct';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Swatches\Model\SwatchAttributesProvider
     */
    private $swatchAttributesProvider;

    /**
     * @param ModuleListInterface $moduleList
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance
     *
     * @param Product $product
     * @return bool|ConfigAdapterInterface
     */
    public function create($product)
    {
        $instance = false;
        $args = ['product' => $product];
        if ($this->moduleList->has(self::SWATCHES_MODULE_NAME)
            && count($this->getSwatchAttributesProvider()->provide($product)) > 0
        ) {
            $instance = $this->objectManager->create(
                Swatches::class,
                $args
            );
        } elseif ($this->moduleList->has(self::CONFIGURABLE_PRODUCT_MODULE_NAME)) {
            $instance = $this->objectManager->create(
                ConfigurableProduct::class,
                $args
            );
        }
        return $instance;
    }

    /**
     * Retrieve Configurable Swatches Block Factory instance
     *
     * @return \Magento\Swatches\Model\SwatchAttributesProvider
     */
    private function getSwatchAttributesProvider()
    {
        if (null === $this->swatchAttributesProvider) {
            $this->swatchAttributesProvider = $this->objectManager->create(
                \Magento\Swatches\Model\SwatchAttributesProvider::class
            );
        }
        return $this->swatchAttributesProvider;
    }
}
