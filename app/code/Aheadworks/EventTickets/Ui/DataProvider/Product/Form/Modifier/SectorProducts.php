<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Ui\Component\Listing\Columns\Price as PriceModifier;

/**
 * Class SectorProducts
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class SectorProducts extends AbstractModifier
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
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;

    /**
     * @var PriceModifier
     */
    private $priceModifier;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param Status $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param PriceModifier $priceModifier
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository,
        PriceModifier $priceModifier
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->priceModifier = $priceModifier;
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
        $sectorsContainerPath = $this->arrayManager->findPath('sectors_container', $meta, null, 'children');
        if (!$sectorsContainerPath) {
            return $meta;
        }

        $meta = $this->arrayManager->merge(
            $sectorsContainerPath . '/children',
            $meta,
            [
                'sector_products_config' => $this->getSectorProductsConfig()
            ]
        );

        return $meta;
    }

    /**
     * Retrieve prepared sector config data for the current product
     *
     * @param array $productSectorConfigData
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPreparedProductSectorConfigData($productSectorConfigData)
    {
        if (!empty($productSectorConfigData) && (is_array($productSectorConfigData))) {
            foreach ($productSectorConfigData as &$productSectorConfigDataRow) {
                if (isset($productSectorConfigDataRow[ProductSectorInterface::SECTOR_PRODUCTS])
                    && (is_array($productSectorConfigDataRow[ProductSectorInterface::SECTOR_PRODUCTS]))
                ) {
                    foreach ($productSectorConfigDataRow[ProductSectorInterface::SECTOR_PRODUCTS] as &$sectorProduct) {
                        $product = $this->productRepository->getById(
                            $sectorProduct[ProductSectorProductInterface::PRODUCT_ID]
                        );
                        $productData = [
                            'id' => $product->getId(),
                            'position' => $sectorProduct[ProductSectorProductInterface::POSITION],
                            'name' => $product->getName(),
                            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                            'sku' => $product->getSku(),
                            'status' => $this->status->getOptionText($product->getStatus()),
                            'attribute_set' => $this->attributeSetRepository
                                ->get($product->getAttributeSetId())
                                ->getAttributeSetName(),
                            'type' => $product->getTypeId(),
                            'price' => $product->getPrice()
                        ];

                        $sectorProduct = $productData;
                    }
                    $this->priceModifier->setData('name', 'price');
                    $dataMap = $this->priceModifier->prepareDataSource([
                        'data' => [
                            'items' => $productSectorConfigDataRow[ProductSectorInterface::SECTOR_PRODUCTS]
                        ]
                    ]);
                    $productSectorConfigDataRow[ProductSectorInterface::SECTOR_PRODUCTS] = $dataMap['data']['items'];
                }
            }
        }
        return $productSectorConfigData;
    }

    /**
     * Returns sector products config
     *
     * @return array
     */
    private function getSectorProductsConfig()
    {
        $modalTarget = '${ $.parentName }.product_listing_modal';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'label' => false
                    ],
                ],
            ],
            'children' => [
                'select_products_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                        '__disableTmpl' => [
                                            'targetName' => false,
                                        ]
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.aw_event_tickets_product_listing',
                                        '__disableTmpl' => [
                                            'targetName' => false,
                                        ]
                                    ],
                                    [
                                        'targetName' => '${ $.parentName }.sector_products',
                                        'actionName' => 'reinitRecordData',
                                        '__disableTmpl' => [
                                            'targetName' => false,
                                        ]
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.aw_event_tickets_product_listing',
                                        'actionName' => 'render',
                                        '__disableTmpl' => [
                                            'targetName' => false,
                                        ]
                                    ],
                                ],
                                'title' => __('Select Products'),
                                'sortOrder' => 30,
                            ],
                        ],
                    ],
                ],
                'product_listing_modal' => $this->getProductListingModal(),
                'sector_products' => $this->getSectorProductsGrid()
            ],
        ];
    }

    /**
     * Prepares config for products modal slide-out panel
     *
     * @return array
     */
    private function getProductListingModal()
    {
        $modalTarget = '${ $.parentName }.product_listing_modal';
        $listing = 'aw_event_tickets_product_listing';
        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'modalClass' => 'aw-et__select-products-modal',
                            'title' => __('Select Products'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => $modalTarget . '.aw_event_tickets_product_listing',
                                            'actionName' => 'save',
                                            '__disableTmpl' => [
                                                'targetName' => false,
                                            ]
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listing => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'component' => 'Aheadworks_EventTickets/js/product/new/sector-products/insert-listing',
                                'dataScope' => $listing,
                                'externalProvider' => $listing . '.' . $listing . '_data_source',
                                'selectionsProvider' => $listing . '.' . $listing . '.product_columns.ids',
                                'ns' => $listing,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false]
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Prepares config for sector products grid
     *
     * @return array
     */
    private function getSectorProductsGrid()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => '${ $.dataScope }.aw_event_tickets_product_listing',
                        'map' => [
                            'id' => 'entity_id',
                            'name' => 'name',
                            'status' => 'status_text',
                            'attribute_set' => 'attribute_set_text',
                            'sku' => 'sku',
                            'price' => 'price',
                            'thumbnail' => 'thumbnail_src',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => [
                                'insertData' => false,
                            ]
                        ],
                        'sortOrder' => 40,
                        '__disableTmpl' => [
                            'dataProvider' => false,
                        ]
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->getGridColumns(),
                ],
            ],
        ];
    }

    /**
     * Retrieve grid columns
     *
     * @return array
     */
    private function getGridColumns()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'thumbnail' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
                            'dataType' => Text::NAME,
                            'dataScope' => 'thumbnail',
                            'fit' => true,
                            'label' => __('Thumbnail'),
                            'sortOrder' => 10,
                        ],
                    ],
                ],
            ],
            'name' => $this->getTextColumn('name', false, __('Name'), 20),
            'status' => $this->getTextColumn('status', true, __('Status'), 30),
            'attribute_set' => $this->getTextColumn('attribute_set', false, __('Attribute Set'), 40),
            'sku' => $this->getTextColumn('sku', true, __('SKU'), 50),
            'price' => $this->getTextColumn('price', true, __('Price'), 60),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 80,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    private function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
