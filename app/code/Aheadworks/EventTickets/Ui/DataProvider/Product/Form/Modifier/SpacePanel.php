<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\TypeList;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;

/**
 * Class SpacePanel
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class SpacePanel extends AbstractModifier
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
     * @var TypeList
     */
    private $typeList;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param TypeList $typeList
     * @param SectorRepositoryInterface $sectorRepository
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        TypeList $typeList,
        SectorRepositoryInterface $sectorRepository
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->typeList = $typeList;
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (isset($data[$productId][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG])) {
            $data[$productId][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG] =
                $this->getPreparedProductSectorConfigData(
                    $data[$productId][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG]
                );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $sectorConfigAttributePath = $this->arrayManager->findPath(
            static::CONTAINER_PREFIX . ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
            $meta,
            null,
            'children'
        );
        if (!$sectorConfigAttributePath) {
            return $meta;
        }

        $meta = $this->arrayManager->set(
            $sectorConfigAttributePath . '/children',
            $meta,
            [
                'select_space_config_button_container' => $this->getSpaceConfigurationButton(),
                'aw_et_sector_config' => $this->getSectorConfiguration()
            ]
        );

        return $meta;
    }

    /**
     * Retrieve prepared sector config data for the current product
     *
     * @param array $productSectorConfigData
     * @return array
     */
    private function getPreparedProductSectorConfigData($productSectorConfigData)
    {
        $preparedProductSectorConfigData = [];
        if (!empty($productSectorConfigData) && (is_array($productSectorConfigData))) {
            foreach ($productSectorConfigData as $productSectorConfigDataRow) {
                if (!empty($productSectorConfigDataRow)
                    && (is_array($productSectorConfigDataRow))
                ) {
                    $preparedProductSectorConfigDataRow = $productSectorConfigDataRow;
                    $preparedProductSectorConfigDataRow['sector'] =
                        $this->getSectorName($productSectorConfigDataRow['sector_id']);
                    $preparedProductSectorConfigData[] = $preparedProductSectorConfigDataRow;
                }
            }
        }
        return $preparedProductSectorConfigData;
    }

    /**
     * Retrieve sector name
     *
     * @param int $sectorId
     * @return string
     */
    private function getSectorName($sectorId)
    {
        $sectorName = '';
        try {
            /** @var SectorInterface $sector */
            $sector = $this->sectorRepository->get($sectorId);
            $sectorName = $sector->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
        }
        return $sectorName;
    }

    /**
     * Returns configuration button config
     *
     * @return array
     */
    private function getSpaceConfigurationButton()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'label' => ''
                    ],
                ],
            ],
            'children' => [
                'select_space_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => 'product_form.product_form.awEtSpaceConfigurationModal',
                                        'actionName' => 'trigger',
                                        'params' => ['active', true],
                                    ],
                                    [
                                        'targetName' => 'product_form.product_form.awEtSpaceConfigurationModal',
                                        'actionName' => 'openModal',
                                    ],
                                ],
                                'title' => __('Select Space Configuration'),
                                'sortOrder' => 30,
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve space configuration
     *
     * @return array
     */
    private function getSectorConfiguration()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'template' => 'Aheadworks_EventTickets/ui/dynamic-rows/collapsible',
                        'component' => 'Aheadworks_EventTickets/js/dynamic-rows/dynamic-rows-with-tooltip',
                        'dndConfig' => [
                            'enabled' => false
                        ],
                        'additionalClasses' => 'admin__field-wide',
                        'columnsHeader' => false,
                        'addButton' => false
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
                                'headerLabel' => __('Sector Configuration'),
                                'component' => 'Aheadworks_EventTickets/js/dynamic-rows/record-collapsible',
                                'showDeleteButton' => false,
                                'imports' => [
                                    'label' => '${ $.name }' . '.sectors_container.sector:value',
                                    '__disableTmpl' => [
                                        'label' => false
                                    ]
                                ]
                            ],
                        ],
                    ],
                    'children' => [
                        'sectors_container' => [
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
                            'children' => $this->getSectorConfigurationFields()
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve sector configuration fields
     *
     * @return array
     */
    private function getSectorConfigurationFields()
    {
        $toolTipDescription = __('Leave empty on all ticket types to apply to all of them.');
        return [
            'sector_id' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Form\Field::NAME,
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/text',
                            'visible' => false,
                            'dataScope' => 'sector_id',
                            'formElement' => Form\Element\Input::NAME,
                            'label' => __('Sector Id'),
                            'sortOrder' => 10,
                        ],
                    ],
                ],
            ],
            'sector' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Form\Field::NAME,
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/text',
                            'visible' => false,
                            'dataScope' => 'sector',
                            'formElement' => Form\Element\Input::NAME,
                            'label' => __('Sector Name'),
                            'sortOrder' => 20,
                        ],
                    ],
                ],
            ],
            'sector_tickets' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'dynamicRows',
                            'additionalClasses' => 'admin__field-wide aw-event-tickets sectors',
                            'defaultRecord' => true,
                            'sortOrder' => 30,
                            'template' => 'Aheadworks_EventTickets/ui/dynamic-rows/default-with-tooltip',
                            'component' => 'Aheadworks_EventTickets/js/dynamic-rows/dynamic-rows-with-tooltip',
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
                                    'positionProvider' => 'position'
                                ],
                            ],
                        ],
                        'children' => [
                            'type_id' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Form\Field::NAME,
                                            'dataType' => Form\Element\DataType\Number::NAME,
                                            'dataScope' => 'type_id',
                                            'formElement' => Form\Element\Select::NAME,
                                            'label' => __('Ticket Type'),
                                            'options' => $this->typeList->toOptionArray(),
                                            'fit' => true,
                                            'validation' => [
                                                'required-entry' => true
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                            'early_bird_price' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'component' =>
                                                'Aheadworks_EventTickets/js/ui/form/element/switchable-price',
                                            'componentType' => Form\Field::NAME,
                                            'dataType' => Form\Element\DataType\Text::NAME,
                                            'dataScope' => 'early_bird_price',
                                            'formElement' => Form\Element\Input::NAME,
                                            'additionalClasses' => [
                                                'price-field' => true
                                            ],
                                            'label' => __('Early Bird Price')
                                        ],
                                    ],
                                ],
                            ],
                            'price' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Form\Field::NAME,
                                            'dataType' => Form\Element\DataType\Text::NAME,
                                            'dataScope' => 'price',
                                            'formElement' => Form\Element\Input::NAME,
                                            'additionalClasses' => [
                                                'price-field' => true
                                            ],
                                            'label' => __('Regular Price'),
                                            'validation' => [
                                                'required-entry' => true
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                            'last_days_price' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'component' =>
                                                'Aheadworks_EventTickets/js/ui/form/element/switchable-price',
                                            'componentType' => Form\Field::NAME,
                                            'dataType' => Form\Element\DataType\Text::NAME,
                                            'dataScope' => 'last_days_price',
                                            'formElement' => Form\Element\Input::NAME,
                                            'additionalClasses' => [
                                                'price-field' => true
                                            ],
                                            'label' => __('Last Day(s) Price')
                                        ],
                                    ],
                                ],
                            ],
                            'personal_option_uids' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'formElement' => 'select',
                                            'componentType' => 'field',
                                            'component' => 'Aheadworks_EventTickets/js/product/ticket-type-options',
                                            'filterOptions' => true,
                                            'chipsEnabled' => true,
                                            'disableLabel' => true,
                                            'showPath' => false,
                                            'label' => __('Custom options per ticket type'),
                                            'levelsVisibility' => '1',
                                            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                            'config' => [
                                                'dataScope' => 'personal_option_uids',
                                                'sortOrder' => 10,
                                            ],
                                            'headerTooltipTpl' => 'ui/form/element/helper/tooltip',
                                            'headerTooltip' => [
                                                'description' => $toolTipDescription
                                            ],
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
                            'position' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Form\Field::NAME,
                                            'dataType' => Form\Element\DataType\Text::NAME,
                                            'dataScope' => 'position',
                                            'formElement' => Form\Element\Input::NAME,
                                            'visible' => false,
                                            'additionalClasses' => [
                                                '_hidden' => true
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}
