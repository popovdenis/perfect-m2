<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOptionRepository;
use Aheadworks\EventTickets\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions\Labels;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions\Values;

/**
 * Class PersonalOptions
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute
 */
class PersonalOptions implements CustomizerInterface
{
    /**#@+
     * Constants defined for names of corresponding columns
     */
    const OPTION_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option';
    const OPTION_TYPE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_type';
    const OPTION_SORT_ORDER_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_sort_order';
    const OPTION_IS_REQUIRED_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_is_required';
    const OPTION_IS_APPLIED_TO_ALL_TICKET_TYPES_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '_option_is_applied_to_all_ticket_types';
    /**#@-*/

    /**
     * Symbol between sectors names and its configuration parameters.
     */
    const OPTIONS_SEPARATOR = "\n";

    /**
     * @var PersonalOptionRepository
     */
    private $personalOptionRepository;

    /**
     * @var AttributeFormatterPool
     */
    private $attributeFormatterPool;

    /**
     * @var Labels
     */
    private $labelsCustomizer;

    /**
     * @var Values
     */
    private $valuesCustomizer;

    /**
     * @var array
     */
    private $personalOptionsColumns = [
        self::OPTION_COLUMN_ID,
        self::OPTION_TYPE_COLUMN_ID,
        self::OPTION_SORT_ORDER_COLUMN_ID,
        self::OPTION_IS_REQUIRED_COLUMN_ID,
        self::OPTION_IS_APPLIED_TO_ALL_TICKET_TYPES_COLUMN_ID,
    ];

    /**
     * @param PersonalOptionRepository $personalOptionRepository
     * @param AttributeFormatterPool $attributeFormatterPool
     * @param Labels $labelsCustomizer
     * @param Values $valuesCustomizer
     */
    public function __construct(
        PersonalOptionRepository $personalOptionRepository,
        AttributeFormatterPool $attributeFormatterPool,
        Labels $labelsCustomizer,
        Values $valuesCustomizer
    ) {
        $this->personalOptionRepository = $personalOptionRepository;
        $this->attributeFormatterPool = $attributeFormatterPool;
        $this->labelsCustomizer = $labelsCustomizer;
        $this->valuesCustomizer = $valuesCustomizer;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($eventTicketsAttributesProductsData)
    {
        $preparedData = [];
        $optionFormatter = $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS . '/option/option_uid'
        );
        try {
            foreach ($eventTicketsAttributesProductsData as $productId => $productData) {
                $productOptionsObjects = $this->personalOptionRepository->getByProductId(
                    $productId,
                    StoreOptions::ALL_STORE_VIEWS
                );

                $preparedProductData = $productData;

                $personalOptionsData = [];
                foreach ($this->personalOptionsColumns as $columnName) {
                    $personalOptionsData[$columnName] = '';
                }

                /** @var ProductPersonalOptionInterface $productSectorObject */
                foreach ($productOptionsObjects as $productOptionObject) {
                    $personalOptionsData[self::OPTION_COLUMN_ID] .=
                        $optionFormatter->getFormattedValue($productOptionObject->getUid())
                        . self::OPTIONS_SEPARATOR;

                    $personalOptionsData[self::OPTION_TYPE_COLUMN_ID] .=
                        $productOptionObject->getType()
                        . self::OPTIONS_SEPARATOR;
                    $personalOptionsData[self::OPTION_SORT_ORDER_COLUMN_ID] .=
                        $productOptionObject->getSortOrder()
                        . self::OPTIONS_SEPARATOR;
                    $personalOptionsData[self::OPTION_IS_REQUIRED_COLUMN_ID] .=
                        $productOptionObject->isRequire()
                        . self::OPTIONS_SEPARATOR;
                    $personalOptionsData[self::OPTION_IS_APPLIED_TO_ALL_TICKET_TYPES_COLUMN_ID] .=
                        $productOptionObject->isApplyToAllTicketTypes()
                        . self::OPTIONS_SEPARATOR;
                }

                $preparedProductData = array_merge(
                    $preparedProductData,
                    $personalOptionsData,
                    $this->labelsCustomizer->getPreparedProductData($productOptionsObjects),
                    $this->valuesCustomizer->getPreparedProductData($productOptionsObjects)
                );

                $preparedData[$productId] = $preparedProductData;
            }
        } catch (\Exception $exception) {
        }

        return $preparedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderColumns()
    {
        return array_merge(
            $this->personalOptionsColumns,
            $this->labelsCustomizer->getHeaderColumns(),
            $this->valuesCustomizer->getHeaderColumns()
        );
    }
}
