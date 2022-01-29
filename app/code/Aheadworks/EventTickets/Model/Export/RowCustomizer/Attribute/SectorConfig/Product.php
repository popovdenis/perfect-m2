<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig;

/**
 * Class Product
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig
 */
class Product
{
    /**#@+
     * Constants defined for names of corresponding columns
     */
    const SECTOR_PRODUCT_SKU_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_product_sku';
    const SECTOR_PRODUCT_POSITION_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_product_position';
    /**#@-*/

    /**
     * @var array
     */
    private $sectorConfigProductColumns = [
        self::SECTOR_PRODUCT_SKU_COLUMN_ID,
        self::SECTOR_PRODUCT_POSITION_COLUMN_ID,
    ];

    /**
     * @var AttributeFormatterPool
     */
    private $attributeFormatterPool;

    /**
     * @param AttributeFormatterPool $attributeFormatterPool
     */
    public function __construct(
        AttributeFormatterPool $attributeFormatterPool
    ) {
        $this->attributeFormatterPool = $attributeFormatterPool;
    }

    /**
     * Prepare data for export
     *
     * @param ProductSectorInterface[] $productSectorObjects
     * @return array
     */
    public function getPreparedProductData($productSectorObjects)
    {
        $preparedProductData = [];
        foreach ($this->sectorConfigProductColumns as $columnName) {
            $preparedProductData[$columnName] = '';
        }
        $productFormatter = $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '/sector_products/product_id'
        );
        /** @var ProductSectorInterface $productSectorObject */
        foreach ($productSectorObjects as $productSectorObject) {
            /** @var ProductSectorProductInterface $sectorProduct */
            foreach ($productSectorObject->getSectorProducts() as $sectorProduct) {
                $preparedProductData[self::SECTOR_PRODUCT_SKU_COLUMN_ID] .=
                    $productFormatter->getFormattedValue($sectorProduct->getProductId())
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::SECTOR_PRODUCT_POSITION_COLUMN_ID] .=
                    $sectorProduct->getPosition()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
            }
            foreach ($this->sectorConfigProductColumns as $columnName) {
                $preparedProductData[$columnName] .= SectorConfig::SECTORS_SEPARATOR;
            }
        }

        return $preparedProductData;
    }

    /**
     * Retrieve headers columns
     *
     * @return array
     */
    public function getHeaderColumns()
    {
        return $this->sectorConfigProductColumns;
    }
}
