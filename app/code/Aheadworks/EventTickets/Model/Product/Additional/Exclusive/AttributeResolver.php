<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class AttributeResolver
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive
 */
class AttributeResolver
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(
        BooleanUtils $booleanUtils
    ) {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * Resolve aw et exclusive product
     *
     * @param Product $product
     * @return bool
     */
    public function resolveExclusive($product)
    {
        $isExclusiveProduct = $product->getData(ProductAttributeInterface::CODE_AW_ET_EXCLUSIVE_PRODUCT);
        return empty($isExclusiveProduct) ? false : $this->booleanUtils->toBoolean($isExclusiveProduct);
    }
}
