<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;

/**
 * Class Labels
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions
 */
class Labels
{
    /**#@+
     * Constants defined for names of corresponding columns
     */
    const OPTION_LABELS_STORE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_labels_store';
    const OPTION_LABELS_TITLE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_labels_title';
    /**#@-*/

    /**
     * @var array
     */
    private $personalOptionsLabelsColumns = [
        self::OPTION_LABELS_STORE_COLUMN_ID,
        self::OPTION_LABELS_TITLE_COLUMN_ID,
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
     * @param ProductPersonalOptionInterface[] $productPersonalOptions
     * @return array
     */
    public function getPreparedProductData($productPersonalOptions)
    {
        $preparedProductData = [];
        foreach ($this->personalOptionsLabelsColumns as $columnName) {
            $preparedProductData[$columnName] = '';
        }
        $storeFormatter =  $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '/option_labels/store_id'
        );
        /** @var ProductPersonalOptionInterface $optionObject */
        foreach ($productPersonalOptions as $optionObject) {
            /** @var StorefrontLabelsInterface $storefrontLabel */
            foreach ($optionObject->getLabels() as $storefrontLabel) {
                $preparedProductData[self::OPTION_LABELS_STORE_COLUMN_ID] .=
                    $storeFormatter->getFormattedValue($storefrontLabel->getStoreId())
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::OPTION_LABELS_TITLE_COLUMN_ID] .=
                    $storefrontLabel->getTitle()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
            }
            foreach ($this->personalOptionsLabelsColumns as $columnName) {
                $preparedProductData[$columnName] .= PersonalOptions::OPTIONS_SEPARATOR;
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
        return $this->personalOptionsLabelsColumns;
    }
}
