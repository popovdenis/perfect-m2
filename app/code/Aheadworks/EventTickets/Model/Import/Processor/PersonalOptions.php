<?php
namespace Aheadworks\EventTickets\Model\Import\Processor;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions as PersonalOptionsRowCustomizer;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions\Labels
    as LabelsPersonalOptionsRowCustomizer;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions\Values
    as ValuesPersonalOptionsRowCustomizer;
use Aheadworks\EventTickets\Model\Import\ArrayProcessor;
use Aheadworks\EventTickets\Model\Import\StoreProcessor;
use Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOptionRepository;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\Framework\Api\DataObjectHelper;
use Magento\ImportExport\Model\Import;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterfaceFactory;

/**
 * Class PersonalOptions
 * @package Aheadworks\EventTickets\Model\Import\Processor
 */
class PersonalOptions implements ProcessorInterface
{
    /**
     * @var ArrayProcessor
     */
    private $arrayProcessor;

    /**
     * @var StoreProcessor
     */
    private $storeProcessor;

    /**
     * @var ProductPersonalOptionInterfaceFactory
     */
    private $productPersonalOptionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var PersonalOptionRepository
     */
    private $personalOptionRepository;

    /**
     * @param ArrayProcessor $arrayProcessor
     * @param StoreProcessor $storeProcessor
     * @param ProductPersonalOptionInterfaceFactory $productPersonalOptionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param PersonalOptionRepository $personalOptionRepository
     */
    public function __construct(
        ArrayProcessor $arrayProcessor,
        StoreProcessor $storeProcessor,
        ProductPersonalOptionInterfaceFactory $productPersonalOptionFactory,
        DataObjectHelper $dataObjectHelper,
        PersonalOptionRepository $personalOptionRepository
    ) {
        $this->arrayProcessor = $arrayProcessor;
        $this->storeProcessor = $storeProcessor;
        $this->productPersonalOptionFactory = $productPersonalOptionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->personalOptionRepository = $personalOptionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function processData($rowData, $entity)
    {
        $entityId = $entity->getEntityId();
        $options = $this->parseOptions($rowData);
        $options = $this->arrayProcessor->removeEmptyValuesAndSubArrays($options);
        $options = $this->storeProcessor->changeStoreLabelToId($options);

        $personalOptions = [];
        foreach ($options as &$option) {
            $option[ProductPersonalOptionInterface::PRODUCT_ID] = $entityId;
            $productPersonalOptionDataObject = $this->productPersonalOptionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productPersonalOptionDataObject,
                $option,
                ProductPersonalOptionInterface::class
            );
            $personalOptions[] = $productPersonalOptionDataObject;
        }

        $this->personalOptionRepository->deleteByProductId($entityId);
        $this->personalOptionRepository->save($personalOptions, $entityId);

        $entity->getExtensionAttributes()->setAwEtPersonalOptions($personalOptions);

        return $rowData;
    }

    /**
     * Parse options
     *
     * @param array $rowData
     * @return array
     */
    private function parseOptions($rowData)
    {
        $optionIds = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[PersonalOptionsRowCustomizer::OPTION_COLUMN_ID])
                ? $rowData[PersonalOptionsRowCustomizer::OPTION_COLUMN_ID]
                : ""
        );
        $optionTypes = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[PersonalOptionsRowCustomizer::OPTION_TYPE_COLUMN_ID])
                ? $rowData[PersonalOptionsRowCustomizer::OPTION_TYPE_COLUMN_ID]
                : ""
        );
        $optionSortOrders = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[PersonalOptionsRowCustomizer::OPTION_SORT_ORDER_COLUMN_ID])
                ? $rowData[PersonalOptionsRowCustomizer::OPTION_SORT_ORDER_COLUMN_ID]
                : ""
        );
        $optionRequireds = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[PersonalOptionsRowCustomizer::OPTION_IS_REQUIRED_COLUMN_ID])
                ? $rowData[PersonalOptionsRowCustomizer::OPTION_IS_REQUIRED_COLUMN_ID]
                : ""
        );
        $optionApplieds = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[PersonalOptionsRowCustomizer::OPTION_IS_APPLIED_TO_ALL_TICKET_TYPES_COLUMN_ID])
                ? $rowData[PersonalOptionsRowCustomizer::OPTION_IS_APPLIED_TO_ALL_TICKET_TYPES_COLUMN_ID]
                : ""
        );
        $optionLabels = $this->parseOptionLabels($rowData);
        $optionValues = $this->parseOptionValues($rowData);

        $options = array_combine(
            array_keys($optionIds),
            array_map(
                function ($optionType, $optionSortOrder, $optionRequired, $optionApplied, $labels, $values) {
                    if (empty($optionType)) {
                        return [];
                    }
                    if (empty($optionRequired)) {
                        $optionRequired = '0';
                    }

                    return [
                        ProductPersonalOptionInterface::UID => uniqid(),
                        ProductPersonalOptionInterface::TYPE => $optionType,
                        ProductPersonalOptionInterface::SORT_ORDER => $optionSortOrder,
                        ProductPersonalOptionInterface::IS_REQUIRE => $optionRequired,
                        ProductPersonalOptionInterface::IS_APPLY_TO_ALL_TICKET_TYPES => $optionApplied,
                        ProductPersonalOptionInterface::LABELS => $labels,
                        ProductPersonalOptionInterface::VALUES => $values,
                    ];
                },
                $optionTypes,
                $optionSortOrders,
                $optionRequireds,
                $optionApplieds,
                $optionLabels,
                $optionValues
            )
        );
        return $options;
    }

    /**
     * Parse option labels
     *
     * @param array $rowData
     * @return array
     */
    private function parseOptionLabels($rowData)
    {
        $optionIds = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[PersonalOptionsRowCustomizer::OPTION_COLUMN_ID])
                ? $rowData[PersonalOptionsRowCustomizer::OPTION_COLUMN_ID]
                : ""
        );
        $optionLabelStores = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[LabelsPersonalOptionsRowCustomizer::OPTION_LABELS_STORE_COLUMN_ID])
                ? $rowData[LabelsPersonalOptionsRowCustomizer::OPTION_LABELS_STORE_COLUMN_ID]
                : ""
        );
        $optionLabelTitles = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[LabelsPersonalOptionsRowCustomizer::OPTION_LABELS_TITLE_COLUMN_ID])
                ? $rowData[LabelsPersonalOptionsRowCustomizer::OPTION_LABELS_TITLE_COLUMN_ID]
                : ""
        );

        $labels = array_combine(
            array_keys($optionIds),
            array_map(
                function ($rowStore, $rowLabel) {
                    if (empty($rowStore) || empty($rowLabel)) {
                        return [];
                    }

                    $rowStore = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $rowStore);
                    $rowLabel = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $rowLabel);

                    $label = array_combine(
                        array_keys($rowStore),
                        array_map(
                            function ($store, $title) {
                                if (empty($store) || empty($title)) {
                                    return [];
                                }
                                return [
                                    StorefrontLabelsInterface::STORE_ID => $store,
                                    StorefrontLabelsInterface::TITLE => $title
                                ];
                            },
                            $rowStore,
                            $rowLabel
                        )
                    );
                    return $label;
                },
                $optionLabelStores,
                $optionLabelTitles
            )
        );

        return $labels;
    }

    /**
     * Parse option values
     *
     * @param array $rowData
     * @return array
     */
    private function parseOptionValues($rowData)
    {
        $optionValues = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_COLUMN_ID])
                ? $rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_COLUMN_ID]
                : ""
        );
        $optionValueSortOrders = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_SORT_ORDER_COLUMN_ID])
                ? $rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_SORT_ORDER_COLUMN_ID]
                : ""
        );
        $optionValueLabelStores = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_LABELS_STORE_COLUMN_ID])
                ? $rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_LABELS_STORE_COLUMN_ID]
                : ""
        );
        $optionValueLabelTitles = explode(
            PersonalOptionsRowCustomizer::OPTIONS_SEPARATOR,
            isset($rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_LABELS_TITLE_COLUMN_ID])
                ? $rowData[ValuesPersonalOptionsRowCustomizer::OPTION_VALUES_LABELS_TITLE_COLUMN_ID]
                : ""
        );

        $optionValues = array_combine(
            array_keys($optionValues),
            array_map(
                function ($sortOrder, $labelStore, $labelTitle) {
                    if (empty($sortOrder) || empty($labelStore) || empty($labelTitle)) {
                        return [];
                    }

                    $sortOrder = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $sortOrder);
                    $labelStore = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $labelStore);
                    $labelTitle = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $labelTitle);

                    $values = array_combine(
                        array_keys($sortOrder),
                        array_map(
                            function ($sortOrder, $labelStore, $labelTitle) {
                                if (empty($sortOrder)) {
                                    return [];
                                }
                                $labelStore = explode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $labelStore);
                                $labelTitle = explode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $labelTitle);

                                $labels = array_combine(
                                    array_keys($labelStore),
                                    array_map(
                                        function ($store, $title) {
                                            if (empty($store) || empty($title)) {
                                                return [];
                                            }
                                            return [
                                                StorefrontLabelsInterface::STORE_ID => $store,
                                                StorefrontLabelsInterface::TITLE => $title
                                            ];
                                        },
                                        $labelStore,
                                        $labelTitle
                                    )
                                );

                                return [
                                    ProductPersonalOptionValueInterface::SORT_ORDER => $sortOrder,
                                    ProductPersonalOptionValueInterface::LABELS => $labels
                                ];
                            },
                            $sortOrder,
                            $labelStore,
                            $labelTitle
                        )
                    );
                    return $values;
                },
                $optionValueSortOrders,
                $optionValueLabelStores,
                $optionValueLabelTitles
            )
        );

        return $optionValues;
    }
}
