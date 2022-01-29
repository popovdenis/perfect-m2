<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Magento\ImportExport\Model\Import;

/**
 * Class Values
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions
 */
class Values
{
    /**#@+
     * Constants defined for names of corresponding columns
     */
    const OPTION_VALUES_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_values';
    const OPTION_VALUES_SORT_ORDER_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_values_sort_order';
    const OPTION_VALUES_LABELS_STORE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_values_labels_store';
    const OPTION_VALUES_LABELS_TITLE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_values_labels_title';
    /**#@-*/

    /**
     * @var array
     */
    private $personalOptionsValuesColumns = [
        self::OPTION_VALUES_COLUMN_ID,
        self::OPTION_VALUES_SORT_ORDER_COLUMN_ID,
        self::OPTION_VALUES_LABELS_STORE_COLUMN_ID,
        self::OPTION_VALUES_LABELS_TITLE_COLUMN_ID,
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
        foreach ($this->personalOptionsValuesColumns as $columnName) {
            $preparedProductData[$columnName] = '';
        }
        $optionValueFormatter =  $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '/option_values/value_id'
        );
        /** @var ProductPersonalOptionInterface $optionObject */
        foreach ($productPersonalOptions as $optionObject) {

            /** @var ProductPersonalOptionValueInterface $optionValue */
            foreach ($optionObject->getValues() as $optionValue) {
                $preparedProductData[self::OPTION_VALUES_COLUMN_ID] .=
                    $optionValueFormatter->getFormattedValue($optionValue)
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::OPTION_VALUES_SORT_ORDER_COLUMN_ID] .=
                    $optionValue->getSortOrder()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;

                $preparedProductData = $this->addOptionValueLabelsData($preparedProductData, $optionValue);
            }

            foreach ($this->personalOptionsValuesColumns as $columnName) {
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
        return $this->personalOptionsValuesColumns;
    }

    /**
     * Add labels data for specific option value
     *
     * @param array $productData
     * @param ProductPersonalOptionValueInterface $optionValue
     * @return array
     */
    private function addOptionValueLabelsData($productData, $optionValue)
    {
        $storeFormatter =  $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '/option_values/labels_store_id'
        );
        /** @var StorefrontLabelsInterface $optionValueStorefrontLabel */
        foreach ($optionValue->getLabels() as $optionValueStorefrontLabel) {
            $productData[self::OPTION_VALUES_LABELS_STORE_COLUMN_ID] .=
                $storeFormatter->getFormattedValue($optionValueStorefrontLabel->getStoreId())
                . Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR;
            $productData[self::OPTION_VALUES_LABELS_TITLE_COLUMN_ID] .=
                $optionValueStorefrontLabel->getTitle()
                . Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR;
        }
        $productData[self::OPTION_VALUES_LABELS_STORE_COLUMN_ID] .=
            ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
        $productData[self::OPTION_VALUES_LABELS_TITLE_COLUMN_ID] .=
            ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;

        return $productData;
    }
}
