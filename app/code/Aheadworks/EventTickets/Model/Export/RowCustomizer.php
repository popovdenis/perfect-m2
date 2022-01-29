<?php
namespace Aheadworks\EventTickets\Model\Export;

use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket as EventTicketProductType;
use Magento\Eav\Model\Entity\Collection\AbstractCollection as AbstractEavCollection;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\CustomizerInterface;

/**
 * Class RowCustomizer
 *
 * @package Aheadworks\EventTickets\Model\Export
 */
class RowCustomizer implements RowCustomizerInterface
{
    /**
     * @var array
     */
    private $eventTicketsAttributesProductsData = [];

    /**
     * @var array
     */
    private $eventTicketsAttributesToExport = [];

    /**
     * @var array
     */
    private $simpleEventTicketsAttributes = [
        ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING,
        ProductAttributeInterface::CODE_AW_ET_START_DATE,
        ProductAttributeInterface::CODE_AW_ET_END_DATE,
        ProductAttributeInterface::CODE_AW_ET_VENUE_ID,
        ProductAttributeInterface::CODE_AW_ET_SPACE_ID,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE,
        ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE,
        ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE
    ];

    /**
     * @var array
     */
    private $complexEventTicketsAttributesConfig = [];

    /**
     * @var AttributeFormatterPool
     */
    private $attributeFormatterPool;

    /**
     * @param AttributeFormatterPool $attributeFormatterPool
     * @param array $eventTicketsAttributesToExport
     * @param array $complexEventTicketsAttributesConfig
     */
    public function __construct(
        AttributeFormatterPool $attributeFormatterPool,
        $eventTicketsAttributesToExport = [],
        $complexEventTicketsAttributesConfig = []
    ) {
        $this->attributeFormatterPool = $attributeFormatterPool;
        $this->eventTicketsAttributesToExport = $eventTicketsAttributesToExport;
        $this->complexEventTicketsAttributesConfig = $complexEventTicketsAttributesConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($collection, $productIds)
    {
        if ($collection instanceof AbstractEavCollection) {
            try {
                $eventTicketsProductsData = $this->getProductsBaseData($collection, $productIds);
                $this->eventTicketsAttributesProductsData = $this->getSimpleAttributesProductsData(
                    $eventTicketsProductsData
                );
                $this->eventTicketsAttributesProductsData = $this->addComplexAttributesProductsData(
                    $this->eventTicketsAttributesProductsData
                );
            } catch (LocalizedException $exception) {
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addHeaderColumns($columns)
    {
        $additionalHeaderColumns = [];
        foreach ($this->complexEventTicketsAttributesConfig as
                 $complexAttributeName => $attributeDataResolver) {
            if (in_array($complexAttributeName, $this->eventTicketsAttributesToExport)
                && $attributeDataResolver instanceof CustomizerInterface
            ) {
                $additionalHeaderColumns = array_merge(
                    $additionalHeaderColumns,
                    $attributeDataResolver->getHeaderColumns()
                );
            }
        }
        $baseHeaderColumns = array_diff(
            $this->eventTicketsAttributesToExport,
            array_keys($this->complexEventTicketsAttributesConfig)
        );
        return array_merge($columns, $baseHeaderColumns, $additionalHeaderColumns);
    }

    /**
     * {@inheritdoc}
     */
    public function addData($dataRow, $productId)
    {
        if (!empty($this->eventTicketsAttributesProductsData[$productId])) {
            $dataRow = array_merge($dataRow, $this->eventTicketsAttributesProductsData[$productId]);
        }
        $dataRow = $this->cleanAdditionalAttributes($dataRow);
        return $dataRow;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }

    /**
     * Retrieve base data of event tickets products
     *
     * @param AbstractEavCollection $collection
     * @param array $productIds
     * @return array
     * @throws LocalizedException
     */
    private function getProductsBaseData($collection, $productIds)
    {
        $eventTicketProductCollection = clone $collection;
        $eventTicketProductCollection->addAttributeToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToFilter('type_id', ['eq' => EventTicketProductType::TYPE_CODE]);

        foreach ($this->eventTicketsAttributesToExport as $attributeName) {
            if (in_array($attributeName, $this->simpleEventTicketsAttributes)) {
                $eventTicketProductCollection->addFieldToSelect($attributeName);
            }
        }

        return $eventTicketProductCollection->load()->toArray();
    }

    /**
     * Retrieve formatted values of simple event ticket attributes for products data array
     *
     * @param array $eventTicketsProductsData
     * @return array
     */
    private function getSimpleAttributesProductsData($eventTicketsProductsData)
    {
        $eventTicketsAttributesProductsData = [];
        foreach ($eventTicketsProductsData as $productId => $productData) {
            $eventTicketsAttributesProductData = [];
            foreach ($this->eventTicketsAttributesToExport as $attributeName) {
                $value = isset($productData[$attributeName]) ? $productData[$attributeName] : null;
                $formatter = $this->attributeFormatterPool->getByAttributePath($attributeName);
                if ($formatter) {
                    $eventTicketsAttributesProductData[$attributeName] = $formatter->getFormattedValue($value);
                }
            }
            $eventTicketsAttributesProductsData[$productId] = $eventTicketsAttributesProductData;
        }
        return $eventTicketsAttributesProductsData;
    }

    /**
     * Add formatted values of complex event ticket attributes for products data array
     *
     * @param array $eventTicketsAttributesProductsData
     * @return array
     */
    private function addComplexAttributesProductsData($eventTicketsAttributesProductsData)
    {
        $mergedProductsData = $eventTicketsAttributesProductsData;
        foreach ($this->complexEventTicketsAttributesConfig as
                 $complexAttributeName => $attributeDataResolver) {
            if (in_array($complexAttributeName, $this->eventTicketsAttributesToExport)
                && $attributeDataResolver instanceof CustomizerInterface
            ) {
                $mergedProductsData = $attributeDataResolver->prepareData($mergedProductsData);
            }
        }
        return $mergedProductsData;
    }

    /**
     * Clean additional attributes from data row
     *
     * @param array $dataRow
     * @return array
     */
    private function cleanAdditionalAttributes($dataRow)
    {
        if (isset($dataRow['additional_attributes']) && !empty($dataRow['additional_attributes'])) {
            $additionalAttributes = preg_split('(,(?=\S))', $dataRow['additional_attributes'], -1, PREG_SPLIT_NO_EMPTY);
            $result = [];
            foreach ($additionalAttributes as $attribute) {
                $callbackSearch = function ($etAttribute) use ($attribute) {
                    return strpos($attribute, $etAttribute . '=') !== false;
                };

                if (empty(array_filter($this->simpleEventTicketsAttributes, $callbackSearch))) {
                    $result[] = $attribute;
                }
            }
            $dataRow['additional_attributes'] = implode(',', $result);
        }
        return $dataRow;
    }
}
