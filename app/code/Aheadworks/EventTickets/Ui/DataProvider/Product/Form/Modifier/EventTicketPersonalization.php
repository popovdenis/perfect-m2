<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;
use Aheadworks\EventTickets\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use Aheadworks\EventTickets\Model\Product\PersonalOptions\Config as PersonalOptionsConfig;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class EventTicketPersonalization
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class EventTicketPersonalization extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var PersonalOptionsConfig
     */
    private $personalOptionsConfig;

    /**
     * @var StoreOptions
     */
    private $storeOptions;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param PersonalOptionsConfig $personalOptionsConfig
     * @param StoreOptions $storeOptions
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        PersonalOptionsConfig $personalOptionsConfig,
        StoreOptions $storeOptions,
        StoreManagerInterface $storeManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->personalOptionsConfig = $personalOptionsConfig;
        $this->storeOptions = $storeOptions;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        $productData = &$data[$productId][self::DATA_SOURCE_DEFAULT];

        if (isset($productData[ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS])) {
            $productData[ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS] =
                $this->getPreparedPersonalOptionsData(
                    $productData[ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS]
                );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $customOptionsAttributePath = $this->arrayManager->findPath(
            static::CONTAINER_PREFIX . ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS,
            $meta,
            null,
            'children'
        );
        if ($customOptionsAttributePath) {
            $meta = $this->arrayManager->set(
                $customOptionsAttributePath . '/children',
                $meta,
                [
                    ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS => $this->getPersonalizationConfiguration()
                ]
            );
        }
        $customOptionsPath = $this->arrayManager->findPath(
            'event-ticket-personalization',
            $meta,
            null,
            'children'
        );
        if ($customOptionsPath) {
            $meta = $this->arrayManager->merge(
                $customOptionsPath,
                $meta,
                [
                    'arguments' => ['data' => ['config' => [
                        'sortOrder' => 99
                    ]]]
                ]
            );
        }
        return $meta;
    }

    /**
     * Retrieve prepared personal options data
     *
     * @param array $personalOptions
     * @return array
     */
    private function getPreparedPersonalOptionsData($personalOptions)
    {
        if (!is_array($personalOptions)) {
            return [];
        }
        foreach ($personalOptions as &$option) {
            $option[ProductPersonalOptionInterface::IS_REQUIRE] = $option[ProductPersonalOptionInterface::IS_REQUIRE]
                ? '1'
                : '0';
            $option[ProductPersonalOptionInterface::IS_APPLY_TO_ALL_TICKET_TYPES] =
                $option[ProductPersonalOptionInterface::IS_APPLY_TO_ALL_TICKET_TYPES]
                ? '1'
                : '0';
            if (!isset($option[ProductPersonalOptionInterface::VALUES])
                || !is_array($option[ProductPersonalOptionInterface::VALUES])
            ) {
                continue;
            }

            foreach ($option[ProductPersonalOptionInterface::VALUES] as &$optionValue) {
                $optionValueLabels = [];
                if (isset($optionValue[StorefrontLabelsEntityInterface::LABELS])
                    && is_array($optionValue[StorefrontLabelsEntityInterface::LABELS])
                ) {
                    foreach ($optionValue[StorefrontLabelsEntityInterface::LABELS] as $optionValueLabel) {
                        $optionValueLabels[$optionValueLabel[StorefrontLabelsInterface::STORE_ID]] =
                            $optionValueLabel[StorefrontLabelsInterface::TITLE];
                    }
                }
                $optionValue[StorefrontLabelsEntityInterface::LABELS] = $optionValueLabels;
            }
        }

        return $personalOptions;
    }

    /**
     * Retrieve personalization configuration
     *
     * @return array
     */
    private function getPersonalizationConfiguration()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'template' => 'Aheadworks_EventTickets/ui/dynamic-rows/collapsible',
                        'additionalClasses' => 'admin__field-wide',
                        'columnsHeader' => false,
                        'addButton' => true,
                        'addButtonLabel' => __('Add Option')
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'headerLabel' => __('New Option'),
                                'component' => 'Aheadworks_EventTickets/js/dynamic-rows/record-collapsible',
                                'showDeleteButton' => true,
                                'imports' => [
                                    'label' => '${ $.name }' . '.personalization_fieldset.labels.0.title:value'
                                ],
                                'positionProvider' => 'personalization_fieldset.group_container.sort_order'
                            ],
                        ],
                    ],
                    'children' => [
                        'personalization_fieldset' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'fieldset',
                                        'collapsible' => true,
                                        'label' => '',
                                        'opened' => true,
                                    ],
                                ],
                            ],
                            'children' => [
                                'group_container' => $this->getGroupFields(),
                                'labels' => $this->getOptionTitle(),
                                'values' => $this->getMultipleElementOptions()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve group fields
     *
     * @return array
     */
    private function getGroupFields()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                    ],
                ],
            ],
            'children' => [
                'id' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                'visible' => false,
                                'additionalClasses' => [
                                    '_hidden' => true
                                ],
                                'dataScope' => 'id',
                                'formElement' => Form\Element\Input::NAME,
                                'label' => __('Option Id')
                            ],
                        ],
                    ],
                ],
                'sort_order' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'visible' => false,
                                'additionalClasses' => [
                                    '_hidden' => true
                                ],
                                'dataScope' => 'sort_order',
                                'formElement' => Form\Element\Input::NAME,
                                'label' => __('Sort Order')
                            ],
                        ],
                    ],
                ],
                'type' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'formElement' => Form\Element\Select::NAME,
                                'component' => 'Magento_Catalog/js/custom-options-type',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'dataScope' => 'type',
                                'label' => __('Option Type'),
                                'selectType' => 'optgroup',
                                'options' => $this->getCustomOptionTypes(),
                                'disableLabel' => true,
                                'multiple' => false,
                                'selectedPlaceholders' => [
                                    'defaultPlaceholder' => __('-- Please select --'),
                                ],
                                'validation' => [
                                    'required-entry' => true
                                ],
                                'groupsConfig' => [
                                    'select' => [
                                        'values' => ['dropdown'],
                                        'indexes' => [
                                            'values'
                                        ]
                                    ],
                                ]
                            ],
                        ],
                    ],
                ],
                'is_require' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Required'),
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Checkbox::NAME,
                                'dataScope' => 'require',
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'value' => '1',
                                'valueMap' => [
                                    'true' => '1',
                                    'false' => '0'
                                ],
                            ],
                        ],
                    ],
                ],
                'apply_to_all_ticket_types' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Apply to All Ticket Types'),
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Checkbox::NAME,
                                'dataScope' => 'apply_to_all_ticket_types',
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'value' => '0',
                                'valueMap' => [
                                    'true' => '1',
                                    'false' => '0'
                                ],
                            ],
                        ],
                    ],
                ],
                'uid' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'visible' => false,
                                'additionalClasses' => [
                                    '_hidden' => true
                                ],
                                'dataScope' => 'uid',
                                'formElement' => Form\Element\Input::NAME,
                                'label' => __('Uid'),
                                'component' => 'Aheadworks_EventTickets/js/ui/form/element/uid-field',
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve option title
     *
     * @return array
     */
    private function getOptionTitle()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'additionalClasses' => 'admin__field-wide aw-event-tickets'
                            . ' dynamic-rows storefront_description',
                        'pageSize' => 100,
                        'defaultRecord' => true,
                        'addButton' => $this->storeManager->hasSingleStore() ? false : true,
                        'dndConfig' => [
                            'enabled' => false
                        ]
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Aheadworks_EventTickets/js/dynamic-rows/record'
                            ],
                        ],
                    ],
                    'children' => [
                        'store_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $this->storeManager->hasSingleStore() ? false : true,
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'label' => __('Store View'),
                                        'formElement' => Form\Element\Select::NAME,
                                        'dataScope' => 'store_id',
                                        'disableForDefaultRecord' => true,
                                        'default' => 0,
                                        'options' => $this->storeOptions->toOptionArray(),
                                        'fit' => true,
                                        'additionalClasses' => 'select_field'
                                    ],
                                ],
                            ],
                        ],
                        'title' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'dataScope' => 'title',
                                        'formElement' => Form\Element\Input::NAME,
                                        'label' => __('Option Title'),
                                        'validation' => [
                                            'required-entry' => true
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'action_delete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Element\ActionDelete::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'fit' => true,
                                        'disableForDefaultRecord' => true,
                                        'additionalClasses' => 'action_delete_button'
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve multiple element options
     *
     * @return array
     */
    private function getMultipleElementOptions()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'additionalClasses' => 'admin__field-wide'
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => 'sort_order'
                            ],
                        ],
                    ],
                    'children' => $this->getMultipleElementOptionsChildren()
                ]
            ]
        ];
    }

    /**
     * Retrieve multiple child element options
     *
     * @return array
     */
    private function getMultipleElementOptionsChildren()
    {
        $children = [];
        if ($this->storeManager->hasSingleStore()) {
            $value = $this->storeOptions::ALL_STORE_VIEWS;
            $children['store_label_' . $value] = $this->getOptionFieldConfig($value, __('Value Title'));
        } else {
            foreach ($this->storeOptions->getStoreList() as $option) {
                $children['store_label_' . $option['value']] =
                    $this->getOptionFieldConfig($option['value'], $option['label']);
            }
        }

        $children = array_merge(
            $children,
            [
                'sort_order' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'dataType' => Form\Element\DataType\Number::NAME,
                                'dataScope' => 'sort_order',
                                'formElement' => Form\Element\Input::NAME,
                                'visible' => false,
                                'additionalClasses' => [
                                    '_hidden' => true
                                ]
                            ],
                        ],
                    ],
                ],
                'action_delete' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Element\ActionDelete::NAME,
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'fit' => true
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $children;
    }

    /**
     * Retrieve option field config
     *
     * @param int $value
     * @param string $label
     * @return array
     */
    private function getOptionFieldConfig($value, $label)
    {
        $validation = ($value == 0) ? ['validation' => ['required-entry' => true]] : [];
        return [
            'arguments' => [
                'data' => [
                    'config' => array_merge(
                        [
                            'componentType' => Form\Field::NAME,
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'dataScope' => 'labels.' . $value,
                            'formElement' => Form\Element\Input::NAME,
                            'label' => $label
                        ],
                        $validation
                    )
                ],
            ],
        ];
    }

    /**
     * Get custom option types
     *
     * @return array
     */
    private function getCustomOptionTypes()
    {
        $options = [];
        $groupIndex = 0;

        foreach ($this->personalOptionsConfig->getAll() as $option) {
            $group = [
                'value' => $groupIndex,
                'label' => __($option['label']),
                'optgroup' => []
            ];

            foreach ($option['types'] as $type) {
                $group['optgroup'][] = ['label' => __($type['label']), 'value' => $type['name']];
            }

            if (count($group['optgroup'])) {
                $options[] = $group;
                $groupIndex += 1;
            }
        }

        return $options;
    }
}
