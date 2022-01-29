<?php
// @codingStandardsIgnoreStart
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable;

use Magento\Catalog\Model\Product;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Swatches
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable
 */
class Swatches implements ConfigAdapterInterface
{
    // @codingStandardsIgnoreEnd
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Swatches\Block\Product\Renderer\Configurable
     */
    private $configurableSwatchesBlock;

    /**
     * @var Product
     */
    private $product;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Product $product
    ) {
        $this->objectManager = $objectManager;
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $block = $this->getConfigurableSwatchesBlock();
        $spConfig = \Zend_Json::decode($block->getJsonConfig());

        $options = [
            'isRenderSwatches' => true,
            'spConfig' => $spConfig,
            'jsonSwatchConfig' => \Zend_Json::decode($block->getJsonSwatchConfig()),
            'mediaCallback' => $block->getMediaCallback()
        ];

        return $options;
    }

    /**
     * Retrieve Configurable Swatches Block instance
     *
     * @return \Magento\Swatches\Block\Product\Renderer\Configurable
     */
    private function getConfigurableSwatchesBlock()
    {
        if (null === $this->configurableSwatchesBlock) {
            $configurableSwatchesBlockFactory = $this->objectManager->create(
                \Magento\Swatches\Block\Product\Renderer\ConfigurableFactory::class
            );
            $this->configurableSwatchesBlock = $configurableSwatchesBlockFactory->create();
            $this->configurableSwatchesBlock->setProduct($this->product);
        }
        return $this->configurableSwatchesBlock;
    }
}
