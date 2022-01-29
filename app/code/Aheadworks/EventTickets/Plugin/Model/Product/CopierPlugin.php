<?php
namespace Aheadworks\EventTickets\Plugin\Model\Product;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Copier;

/**
 * Class CopierPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Model\Product
 */
class CopierPlugin
{
    /**
     * After duplicate product
     *
     * @param Copier $subject
     * @param Product $result
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCopy($subject, $result)
    {
        if ($result->getTypeId() == EventTicket::TYPE_CODE) {
            $this->resetProductAttributes($result);
        }

        return $result;
    }

    /**
     * Reset product attributes after duplicated product
     *
     * @param Product $duplicateProduct
     */
    private function resetProductAttributes($duplicateProduct)
    {
        try {
            $storeId = $duplicateProduct->getStoreId();
            $duplicateProduct->addAttributeUpdate(ProductAttributeInterface::CODE_AW_ET_START_DATE, null, $storeId);
            $duplicateProduct->addAttributeUpdate(ProductAttributeInterface::CODE_AW_ET_END_DATE, null, $storeId);
        } catch (\Exception $e) {
        }
    }
}
