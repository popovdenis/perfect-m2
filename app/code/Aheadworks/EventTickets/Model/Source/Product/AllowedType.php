<?php
namespace Aheadworks\EventTickets\Model\Source\Product;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\Product\Type as DefaultProduct;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Downloadable\Model\Product\Type as DownloadableProduct;

/**
 * Class AllowedType
 *
 * @package Aheadworks\EventTickets\Model\Source\Product
 */
class AllowedType implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => DefaultProduct::TYPE_SIMPLE, 'label' => __('Simple Product')],
            ['value' => ConfigurableProduct::TYPE_CODE, 'label' => __('Configurable Product')],
            ['value' => DefaultProduct::TYPE_VIRTUAL, 'label' => __('Virtual Product')],
            ['value' => DownloadableProduct::TYPE_DOWNLOADABLE, 'label' => __('Downloadable Product')]
        ];
    }

    /**
     * Get list of allowed product types to be selected
     *
     * @return array
     */
    public function getTypeList()
    {
        return array_merge(
            $this->getSimpleTypeList(),
            $this->getComplexTypeList()
        );
    }

    /**
     * Get simple list of allowed product types to be selected
     *
     * @return array
     */
    public function getSimpleTypeList()
    {
        return [
            DefaultProduct::TYPE_SIMPLE,
            DefaultProduct::TYPE_VIRTUAL,
            DownloadableProduct::TYPE_DOWNLOADABLE
        ];
    }

    /**
     * Get complex list of allowed product types to be selected
     *
     * @return array
     */
    public function getComplexTypeList()
    {
        return [ConfigurableProduct::TYPE_CODE];
    }
}
