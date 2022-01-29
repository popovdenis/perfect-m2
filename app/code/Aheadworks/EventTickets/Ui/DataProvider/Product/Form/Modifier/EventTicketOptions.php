<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * Class EventTicketOptions
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class EventTicketOptions extends AbstractModifier
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
     * @var Config
     */
    private $config;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param Config $config
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        Config $config
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();
        $this->modifyRequireShippingData($productId, $data);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this
            ->modifyEventTicketOptionsTab($meta)
            ->modifyEventStartDate($meta)
            ->modifyEventEndDate($meta)
            ->modifyRequireShipping($meta)
            ->modifySpaceIdAttribute($meta)
            ->modifyVenueIdAttribute($meta)
            ->modifyTicketSellingDeadline($meta)
            ->modifyEarlyBirdEndDate($meta)
            ->modifyLastDaysStartDate($meta);

        return $meta;
    }

    /**
     * Modify event ticket options tab
     *
     * @param array $meta
     * @return $this
     */
    private function modifyEventTicketOptionsTab(&$meta)
    {
        $tabPath  = $this->arrayManager->findPath('event-ticket-options', $meta, null, 'children');
        if (!$tabPath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'opened' => true
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($tabPath, $meta, $config);

        return $this;
    }

    /**
     * Modify event start date attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyEventStartDate(&$meta)
    {
        $startDateAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_START_DATE, $meta, null, 'children');
        if (!$startDateAttributePath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'options' => [
                            'dateFormat' => 'MMM d, y',
                            'timeFormat' => 'h:mm a',
                            'showsTime' => true
                        ]
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($startDateAttributePath, $meta, $config);

        return $this;
    }

    /**
     * Modify event end date attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyEventEndDate(&$meta)
    {
        $endDateAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_END_DATE, $meta, null, 'children');
        if (!$endDateAttributePath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'options' => [
                            'dateFormat' => 'MMM d, y',
                            'timeFormat' => 'h:mm a',
                            'showsTime' => true
                        ]
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($endDateAttributePath, $meta, $config);

        return $this;
    }

    /**
     * Modify require shipping attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyRequireShipping(&$meta)
    {
        $requireShippingAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING, $meta, null, 'children');
        if (!$requireShippingAttributePath) {
            return $this;
        }

        $usedDefault = $this->locator->getProduct()
                ->getData(ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING) === null;
        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'service' => [
                            'template' =>
                                'Aheadworks_EventTickets/ui/form/element/helper/service-settings',
                            'configSettingsUrl' =>
                                $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/aw_event_tickets'),
                            'label' => __('Use option from')
                        ],
                        'usedDefault' => $usedDefault,
                        'disabled' => $usedDefault,
                        'validation' => [
                            'validate-select' => true
                        ]
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($requireShippingAttributePath, $meta, $config);
        return $this;
    }

    /**
     * Modify require shipping data
     *
     * @param int $productId
     * @param array $data
     * @return $this
     */
    private function modifyRequireShippingData($productId, &$data)
    {
        $isSetRequireShipping = isset(
            $data[$productId][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING]
        );

        if (!$isSetRequireShipping) {
            $requireShippingVal = $this->config->isTicketRequireShipping() ? 1 : 0;
            $data[$productId][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING] =
                (string)$requireShippingVal;
        }

        return $this;
    }

    /**
     * Modify space id attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifySpaceIdAttribute(&$meta)
    {
        $spaceIdAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_SPACE_ID, $meta, null, 'children');
        if (!$spaceIdAttributePath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'visible' => false,
                        'caption' => __('-- Please Select --')
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($spaceIdAttributePath, $meta, $config);
        return $this;
    }

    /**
     * Modify venue id attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyVenueIdAttribute(&$meta)
    {
        $spaceIdAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_VENUE_ID, $meta, null, 'children');
        if (!$spaceIdAttributePath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'visible' => false,
                        'caption' => __('-- Please Select --')
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($spaceIdAttributePath, $meta, $config);
        return $this;
    }

    /**
     * Modify ticket selling deadline attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyTicketSellingDeadline(&$meta)
    {
        $ticketSellingDeadlineAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE, $meta, null, 'children');
        if (!$ticketSellingDeadlineAttributePath) {
            return $this;
        }

        $sellingDeadlineDatePath = 'product_form.product_form.event-ticket-options.one_time_container.'
            . 'container_aw_et_selling_deadline_date.aw_et_selling_deadline_date';
        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Aheadworks_EventTickets/js/ui/form/element/select',
                        'switcherConfig' => [
                            'enabled' => true,
                            'rules' => [
                                [
                                    'value' => TicketSellingDeadline::EVENT_START_DATE,
                                    'actions' => [
                                        [
                                            'target' => $sellingDeadlineDatePath,
                                            'callback' => 'hide'
                                        ]
                                    ]
                                ],
                                [
                                    'value' => TicketSellingDeadline::EVENT_END_DATE,
                                    'actions' => [
                                        [
                                            'target' => $sellingDeadlineDatePath,
                                            'callback' => 'hide'
                                        ]
                                    ]
                                ],
                                [
                                    'value' => TicketSellingDeadline::CUSTOM_DATE,
                                    'actions' => [
                                        [
                                            'target' => $sellingDeadlineDatePath,
                                            'callback' => 'show'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($ticketSellingDeadlineAttributePath, $meta, $config);
        return $this;
    }

    /**
     * Modify early bird price end date attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyEarlyBirdEndDate(&$meta)
    {
        $startDateAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE, $meta, null, 'children');
        if (!$startDateAttributePath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'options' => [
                            'dateFormat' => 'MMM d, y',
                            'timeFormat' => 'h:mm a'
                        ],
                        'tooltip' => [
                            'description' => __('If set, the early bird tickets price will be applied '
                                .'to the tickets until the specified date.')
                        ],
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($startDateAttributePath, $meta, $config);

        return $this;
    }

    /**
     * Modify last days price start date attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyLastDaysStartDate(&$meta)
    {
        $startDateAttributePath  = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE, $meta, null, 'children');
        if (!$startDateAttributePath) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'options' => [
                            'dateFormat' => 'MMM d, y',
                            'timeFormat' => 'h:mm a'
                        ],
                        'tooltip' => [
                            'description' => __('If set, the special price will be applied from the specified date '
                                .'and up until the Ticket Selling Deadline date.')
                        ],
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($startDateAttributePath, $meta, $config);

        return $this;
    }
}
