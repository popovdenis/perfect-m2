<?php
// @codingStandardsIgnoreStart
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable;

use Magento\Catalog\Model\Product;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ConfigurableProduct
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable
 */
class ConfigurableProduct implements ConfigAdapterInterface
{
    // @codingStandardsIgnoreEnd
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
     */
    private $configurableBlock;

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
        $block = $this->getConfigurableBlock();
        $spConfig = \Zend_Json::decode($block->getJsonConfig());

        $options = [
            'isRenderSwatches' => false,
            'attributes' => $this->getAttributes($block),
            'gallerySwitchStrategy' =>
                $block->getVar('gallery_switch_strategy', 'Magento_ConfigurableProduct') ?: 'replace',
            'spConfig' => $spConfig
        ];

        return $options;
    }

    /**
     * Retrieve configurable attributes
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $block
     * @return array
     */
    private function getAttributes($block)
    {
        $options = [];
        $attributes = $block->decorateArray($block->getAllowAttributes());
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attributeId = $attribute->getAttributeId();
            $productAttr = $attribute->getProductAttribute();
            $options[$attributeId] = [
                'id' => $attributeId,
                'code' => $productAttr->getAttributeCode(),
                'label' => $productAttr->getStoreLabel()
            ];
        }
        return $options;
    }

    /**
     * Retrieve Configurable Block Factory instance
     *
     * @return \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
     */
    private function getConfigurableBlock()
    {
        if (null === $this->configurableBlock) {
            $configurableBlockFactory = $this->objectManager->create(
                \Magento\ConfigurableProduct\Block\Product\View\Type\ConfigurableFactory::class
            );
            $this->configurableBlock = $configurableBlockFactory->create();
            $this->configurableBlock->setProduct($this->product);
        }
        return $this->configurableBlock;
    }
}
