<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\TicketSellingDeadline;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\WeekDays;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\ScheduleType as ScheduleTypeSource;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Date;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Boolean;

/**
 * Class ScheduleOptions
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class ScheduleOptions extends AbstractModifier
{
    /**
     * One-time container name
     */
    const ONE_TIME_CONTAINER_NAME = 'one_time_container';

    /**
     * Recurring container name
     */
    const RECURRING_CONTAINER_NAME = 'recurring_container';

    /**#@+
     * Recurring containers name
     */
    const RECURRING_DAILY_CONTAINER_NAME = 'daily_container';
    const RECURRING_WEEKLY_CONTAINER_NAME = 'weekly_container';
    const RECURRING_MONTHLY_CONTAINER_NAME = 'monthly_container';
    /**#@-*/

    /**
     * Weeks count and start date container name
     */
    const WEEKLY_START_DATE_AND_COUNT_CONTAINER = 'weeks_count_and_start_date_container';

    /**
     * Correction fields container name
     */
    const CORRECTION_FIELDS_CONTAINER_NAME = 'correction_container';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var WeekDays
     */
    private $weekDaysSource;

    /**
     * @var TicketSellingDeadline
     */
    private $ticketSellingDeadlineSource;

    /**
     * @var int
     */
    private $currentSortOrder = 30;

    /**
     * @var array
     */
    private $oneTimeScheduleFields = [
        ProductAttributeInterface::CODE_AW_ET_START_DATE,
        ProductAttributeInterface::CODE_AW_ET_END_DATE,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE,
        ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE,
        ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE
    ];

    /**
     * @var array
     */
    private $recurringScheduleFields = [
        ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE_TYPE
    ];

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param Converter $converter
     * @param WeekDays $weekDaysSource
     * @param TicketSellingDeadline $ticketSellingDeadlineSource
     * @param array $oneTimeScheduleFields
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        Converter $converter,
        WeekDays $weekDaysSource,
        TicketSellingDeadline $ticketSellingDeadlineSource,
        array $oneTimeScheduleFields = []
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->converter = $converter;
        $this->weekDaysSource = $weekDaysSource;
        $this->ticketSellingDeadlineSource = $ticketSellingDeadlineSource;
        $this->oneTimeScheduleFields = array_merge($this->oneTimeScheduleFields, $oneTimeScheduleFields);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $recurringSchedule = $product->getExtensionAttributes()
            ? $product->getExtensionAttributes()->getAwEtRecurringSchedule()
            : null;

        if ($recurringSchedule) {
            $data[$product->getId()][static::DATA_SOURCE_DEFAULT]
            [ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE]
                = $this->converter->toFormData($recurringSchedule);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this
            ->modifyEventTicketOptions($meta)
            ->modifyScheduleType($meta)
            ->modifyRecurringScheduleType($meta);

        return $meta;
    }

    /**
     * Modify event ticket options
     *
     * @param array $meta
     * @return $this
     */
    private function modifyEventTicketOptions(&$meta)
    {
        $tabPath  = $this->arrayManager->findPath('event-ticket-options', $meta, null, 'children');
        if (!$tabPath) {
            return $this;
        }

        $children = (array)$this->arrayManager->get($tabPath . '/children', $meta);
        $oneTimeScheduleFields = $this->getScheduleFields($children, $this->oneTimeScheduleFields);
        $recurringScheduleFields = $this->getScheduleFields($children, $this->recurringScheduleFields);
        $newChildren = array_merge(
            array_diff_key($children, $oneTimeScheduleFields, $recurringScheduleFields),
            $this->wrapFields($oneTimeScheduleFields, self::ONE_TIME_CONTAINER_NAME),
            $this->wrapFields(
                array_merge($recurringScheduleFields, $this->getRecurringScheduleFields()),
                self::RECURRING_CONTAINER_NAME
            )
        );

        $meta = $this->arrayManager->set($tabPath . '/children', $meta, $newChildren);

        return $this;
    }

    /**
     * Modify schedule type field
     *
     * @param array $meta
     * @return $this
     */
    private function modifyScheduleType(&$meta)
    {
        $scheduleTypePath = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE, $meta, null, 'children');
        if (!$scheduleTypePath) {
            return $this;
        }

        $oneTimeContainerPath = 'product_form.product_form.event-ticket-options.' . self::ONE_TIME_CONTAINER_NAME;
        $recurringContainerPath = 'product_form.product_form.event-ticket-options.' . self::RECURRING_CONTAINER_NAME;
        $recurringScheduleTypePath = 'product_form.product_form.event-ticket-options.' . self::RECURRING_CONTAINER_NAME
            . '.' . self::CONTAINER_PREFIX . ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE_TYPE
            . '.' . ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE_TYPE;
        $sellingDeadlinePath = 'product_form.product_form.event-ticket-options.' . self::ONE_TIME_CONTAINER_NAME
            . '.' . self::CONTAINER_PREFIX . ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE
            . '.' . ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE;
        $recurringSellingDeadlinePath = 'product_form.product_form.event-ticket-options.'
            . self::RECURRING_CONTAINER_NAME . '.' . ProductRecurringScheduleInterface::SELLING_DEADLINE_TYPE;
        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Aheadworks_EventTickets/js/ui/form/element/schedule-type-select',
                        'provider' => 'product_form.product_form_data_source',
                        'deps' => [
                            $recurringScheduleTypePath,
                            $oneTimeContainerPath,
                            $recurringContainerPath,
                            $recurringSellingDeadlinePath
                        ],
                        'switcherConfig' => [
                            'enabled' => false,
                            'rules' => [
                                [
                                    'value' => ScheduleType::ONE_TIME,
                                    'actions' => [
                                        [
                                            'target' => $oneTimeContainerPath,
                                            'callback' => 'visible',
                                            'params' => [true]
                                        ],
                                        [
                                            'target' => $recurringContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $sellingDeadlinePath,
                                            'callback' => 'triggerUpdateValue'
                                        ]
                                    ]
                                ],
                                [
                                    'value' => ScheduleType::RECURRING,
                                    'actions' => [
                                        [
                                            'target' => $oneTimeContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $recurringContainerPath,
                                            'callback' => 'visible',
                                            'params' => [true]
                                        ],
                                        [
                                            'target' => $recurringScheduleTypePath,
                                            'callback' => 'triggerUpdateValue'
                                        ],
                                        [
                                            'target' => $recurringSellingDeadlinePath,
                                            'callback' => 'triggerUpdateValue'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($scheduleTypePath, $meta, $config);

        return $this;
    }

    /**
     * Modify recurring type field
     *
     * @param array $meta
     * @return $this
     */
    private function modifyRecurringScheduleType(&$meta)
    {
        $scheduleTypePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE_TYPE,
            $meta,
            null,
            'children'
        );
        if (!$scheduleTypePath) {
            return $this;
        }

        $dailyContainerPath = 'product_form.product_form.event-ticket-options.'
            . self::RECURRING_CONTAINER_NAME . '.' . self::RECURRING_DAILY_CONTAINER_NAME;
        $weeklyContainerPath = 'product_form.product_form.event-ticket-options.'
            . self::RECURRING_CONTAINER_NAME . '.' . self::RECURRING_WEEKLY_CONTAINER_NAME;
        $monthlyContainerPath = 'product_form.product_form.event-ticket-options.'
            . self::RECURRING_CONTAINER_NAME . '.' . self::RECURRING_MONTHLY_CONTAINER_NAME;
        $AdditionalDailyContainerPath = 'product_form.product_form.event-ticket-options.'
            . self::RECURRING_CONTAINER_NAME . '.' . ProductRecurringScheduleInterface::DAYS_TO_DISPLAY;
        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Aheadworks_EventTickets/js/ui/form/element/select',
                        'provider' => 'product_form.product_form_data_source',
                        'deps' => [
                            $dailyContainerPath,
                            $weeklyContainerPath,
                            $monthlyContainerPath,
                            $AdditionalDailyContainerPath
                        ],
                        'switcherConfig' => [
                            'enabled' => false,
                            'rules' => [
                                [
                                    'value' => ScheduleTypeSource::DAILY,
                                    'actions' => [
                                        [
                                            'target' => $dailyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [true]
                                        ],
                                        [
                                            'target' => $weeklyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $monthlyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $AdditionalDailyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [true]
                                        ]
                                    ]
                                ],
                                [
                                    'value' => ScheduleTypeSource::WEEKLY,
                                    'actions' => [
                                        [
                                            'target' => $dailyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $weeklyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [true]
                                        ],
                                        [
                                            'target' => $monthlyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $AdditionalDailyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ]
                                    ]
                                ],
                                [
                                    'value' => ScheduleTypeSource::MONTHLY,
                                    'actions' => [
                                        [
                                            'target' => $dailyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $weeklyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ],
                                        [
                                            'target' => $monthlyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [true]
                                        ],
                                        [
                                            'target' => $AdditionalDailyContainerPath,
                                            'callback' => 'visible',
                                            'params' => [false]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $meta = $this->arrayManager->merge($scheduleTypePath, $meta, $config);

        return $this;
    }

    /**
     * Get schedule fields
     *
     * @param array $children
     * @param array $fieldNames
     * @return array
     */
    private function getScheduleFields($children, $fieldNames)
    {
        $fields = [];

        foreach ($fieldNames as $fieldName) {
            $key = self::CONTAINER_PREFIX . $fieldName;
            if (isset($children[$key])) {
                $child = $children[$key];
                $child = $this->arrayManager->merge(
                    'arguments/data/config',
                    $child,
                    [
                        'component' => 'Aheadworks_EventTickets/js/ui/form/components/group',
                        'template' => 'Aheadworks_EventTickets/ui/form/components/group',
                    ]
                );
                $fields[$key] = $child;
            }
        }

        return $fields;
    }

    /**
     * Get recurring schedule fields
     *
     * @return array
     */
    private function getRecurringScheduleFields()
    {
        $idField = [
            ProductRecurringScheduleInterface::ID => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Number::NAME,
                            'template' => 'ui/form/element/hidden',
                            'visible' => false,
                            'dataScope' => ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.'
                                . ProductRecurringScheduleInterface::ID
                        ]
                    ]
                ]
            ]
        ];

        return array_merge(
            $idField,
            $this->wrapFields($this->getDailyRecurringScheduleFields(), self::RECURRING_DAILY_CONTAINER_NAME),
            $this->wrapFields($this->getWeeklyRecurringScheduleFields(), self::RECURRING_WEEKLY_CONTAINER_NAME),
            $this->wrapFields($this->getMonthlyRecurringScheduleFields(), self::RECURRING_MONTHLY_CONTAINER_NAME),
            $this->getDaysToDisplayField(),
            $this->getTimeSlotsConfig(),
            $this->getFilterByTicketQtyField(),
            $this->getMultiselectionTimeSlotsField(),
            $this->getSellingDeadlineConfig()
        );
    }

    /**
     * Get daily recurring schedule fields
     *
     * @return array
     */
    private function getDailyRecurringScheduleFields()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.'. ScheduleTypeSource::DAILY . '_options.';

        return [
            ScheduleOptionInterface::START_DATE => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Date::NAME,
                            'storeTimeZone' => 'UTC',
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::START_DATE,
                            'component' => 'Aheadworks_EventTickets/js/ui/form/element/date',
                            'label' => __('Date From'),
                            'validation' => [
                                'validate-date' => true,
                                'required-entry' => true
                            ]
                        ]
                    ]
                ]
            ],
            ScheduleOptionInterface::END_DATE => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Date::NAME,
                            'storeTimeZone' => 'UTC',
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::END_DATE,
                            'component' => 'Aheadworks_EventTickets/js/ui/form/element/date',
                            'label' => __('Date To'),
                            'validation' => [
                                'validate-date' => true,
                                'required-entry' => true
                            ]
                        ]
                    ]
                ]
            ],
            ScheduleOptionInterface::DISABLED_WEEK_DAYS => [
                'arguments' => [
                    'data' => [
                        'options' => $this->weekDaysSource,
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => MultiSelect::NAME,
                            'dataType' => Date::NAME,
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::DISABLED_WEEK_DAYS,
                            'label' => __('Excluded Days of Week')
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get weekly recurring schedule fields
     *
     * @return array
     */
    private function getWeeklyRecurringScheduleFields()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.'
            . ScheduleTypeSource::WEEKLY . '_options.';

        $fields = [
            ScheduleOptionInterface::WEEK_DAYS => [
                'arguments' => [
                    'data' => [
                        'options' => $this->weekDaysSource,
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => MultiSelect::NAME,
                            'dataType' => Date::NAME,
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::WEEK_DAYS,
                            'label' => __('Days of Week'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'notice' => __('Select the days when the event will be taking place.')
                        ]
                    ]
                ]
            ]
        ];

        $rowFields = [
            ScheduleOptionInterface::WEEKS_COUNT => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Text::NAME,
                            'displayArea' => 'weeks-count',
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::WEEKS_COUNT,
                            'validation' => [
                                'required-entry' => true,
                                'validate-integer' => true,
                                'validate-number-range' => '1-56'
                            ],
                            'additionalClasses' => 'float-left weeks-count'
                        ]
                    ]
                ]
            ],
            ScheduleOptionInterface::START_DATE => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Date::NAME,
                            'displayArea' => 'weekly-start-date',
                            'storeTimeZone' => 'UTC',
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::START_DATE,
                            'component' => 'Aheadworks_EventTickets/js/ui/form/element/date',
                            'validation' => [
                                'validate-date' => true,
                                'required-entry' => true
                            ],
                            'additionalClasses' => 'float-left'
                        ]
                    ]
                ]
            ]
        ];

        $rowFieldsContainer = $this->wrapFields(
            $rowFields,
            self::WEEKLY_START_DATE_AND_COUNT_CONTAINER,
            'Aheadworks_EventTickets/ui/form/components/product/weekly-row-container'
        );

        return array_merge($fields, $rowFieldsContainer);
    }

    /**
     * Get monthly recurring schedule fields
     *
     * @return array
     */
    private function getMonthlyRecurringScheduleFields()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.'
            . ScheduleTypeSource::MONTHLY . '_options.';

        return [
            ScheduleOptionInterface::MONTH_DAYS => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Text::NAME,
                            'dataScope' => $scopePrefix . ScheduleOptionInterface::MONTH_DAYS,
                            'label' => __('Day(s) of Month'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'notice' => __('Specify the days separated by a comma, e.g. 1,11,21,31')
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get time slots config
     *
     * @return array
     */
    private function getTimeSlotsConfig()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE;
        $confirmTitle = __('Do you want to delete the time slot?');
        $confirmContent = __(
            'Warning: There may be tickets purchased on this time slot! '
            .'If you proceed, make sure to contact the customers to rearrange the bookings.'
        );

        return [
            ProductRecurringScheduleInterface::TIME_SLOTS => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'dynamicRows',
                            'label' => __('Time Slots'),
                            'additionalClasses' => 'aw-et__time-slots',
                            'addButton' => true,
                            'component' => 'Aheadworks_EventTickets/js/ui/form/components/time-slot/dynamic-rows',
                            'addButtonLabel' => __('Add'),
                            'dataScope' => $scopePrefix,
                            'dndConfig' => [
                                'enabled' => false
                            ],
                            'required' => true
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
                                    'component' => 'Magento_Ui/js/dynamic-rows/record'
                                ],
                            ],
                        ],
                        'children' => [
                            TimeSlotInterface::START_TIME => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Field::NAME,
                                            'formElement' => Input::NAME,
                                            'dataType' => Text::NAME,
                                            'dataScope' => TimeSlotInterface::START_TIME,
                                            'component' => 'Aheadworks_EventTickets/js/ui/form/element/time',
                                            'label' => __('Start Time'),
                                            'storeTimeZone' => 'UTC',
                                            'validation' => [
                                                'required-entry' => true,
                                                'validate-date' => true
                                            ],
                                            'options' => [
                                                'showsTime' => true,
                                                'timeOnly' => true
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            TimeSlotInterface::END_TIME => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Field::NAME,
                                            'formElement' => Input::NAME,
                                            'dataType' => Text::NAME,
                                            'dataScope' => TimeSlotInterface::END_TIME,
                                            'component' => 'Aheadworks_EventTickets/js/ui/form/element/time',
                                            'label' => __('End Time'),
                                            'storeTimeZone' => 'UTC',
                                            'validation' => [
                                                'required-entry' => true,
                                                'validate-date' => true
                                            ],
                                            'options' => [
                                                'showsTime' => true,
                                                'timeOnly' => true
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'delete' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => ActionDelete::NAME,
                                            'component' => 'Aheadworks_EventTickets/js/ui/form/components/time-slot/'
                                                . 'dynamic-rows/action-delete-with-confirm',
                                            'confirmTitle' => $confirmTitle,
                                            'confirmContent' => $confirmContent
                                        ]
                                    ]
                                ]
                            ],
                            TimeSlotInterface::ID => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Field::NAME,
                                            'formElement' => 'hidden',
                                            'template' => 'ui/form/element/hidden',
                                            'dataType' => Number::NAME,
                                            'dataScope' => TimeSlotInterface::ID,
                                            'visible' => false
                                        ]
                                    ]
                                ]
                            ],
                            TimeSlotInterface::SCHEDULE_ID => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Field::NAME,
                                            'formElement' => 'hidden',
                                            'template' => 'ui/form/element/hidden',
                                            'dataType' => Number::NAME,
                                            'dataScope' => TimeSlotInterface::SCHEDULE_ID,
                                            'visible' => false
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get days to display fields
     *
     * @return array
     */
    private function getDaysToDisplayField()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.';

        return [
            ProductRecurringScheduleInterface::DAYS_TO_DISPLAY => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Number::NAME,
                            'dataScope' => $scopePrefix . ProductRecurringScheduleInterface::DAYS_TO_DISPLAY,
                            'label' => __('The Number of Days to be Displayed in Calendar'),
                            'validation' => [
                                'validate-integer' => true
                            ],
                            'notice' => __('X days to be displayed in Calendar starting from today')
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get filter by available ticket qty fields
     *
     * @return array
     */
    private function getFilterByTicketQtyField()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.';

        return [
            ProductRecurringScheduleInterface::FILTER_BY_TICKET_QTY => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Checkbox::NAME,
                            'dataType' => Boolean::NAME,
                            'dataScope' => $scopePrefix . ProductRecurringScheduleInterface::FILTER_BY_TICKET_QTY,
                            'label' => __('Enable Filter by Available Ticket Qty'),
                            'prefer' => 'toggle',
                            'valueMap' => [
                                'false' => 0,
                                'true' => 1,
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get multiselection time slots fields
     *
     * @return array
     */
    private function getMultiselectionTimeSlotsField()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.';

        return [
            ProductRecurringScheduleInterface::MULTISELECTION_TIME_SLOTS => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Checkbox::NAME,
                            'dataType' => Boolean::NAME,
                            'dataScope' => $scopePrefix . ProductRecurringScheduleInterface::MULTISELECTION_TIME_SLOTS,
                            'label' => __('Time-slots Multiselection'),
                            'prefer' => 'toggle',
                            'valueMap' => [
                                'false' => 0,
                                'true' => 1,
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get selling deadline config
     *
     * @return array
     */
    private function getSellingDeadlineConfig()
    {
        $scopePrefix = ProductAttributeInterface::CODE_AW_ET_RECURRING_SCHEDULE . '.';
        $correctionScopePrefix = ProductRecurringScheduleInterface::SELLING_DEADLINE_CORRECTION . '.';
        $correctionContainerPath = 'product_form.product_form.event-ticket-options.'
            . self::RECURRING_CONTAINER_NAME . '.' . self::CORRECTION_FIELDS_CONTAINER_NAME;

        $fields = [
            ProductRecurringScheduleInterface::SELLING_DEADLINE_TYPE => [
                'arguments' => [
                    'data' => [
                        'options' => $this->ticketSellingDeadlineSource,
                        'config' => [
                            'component' => 'Aheadworks_EventTickets/js/ui/form/element/select',
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataType' => Text::NAME,
                            'dataScope' => $scopePrefix . ProductRecurringScheduleInterface::SELLING_DEADLINE_TYPE,
                            'label' => __('Tickets Selling Deadline'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'switcherConfig' => [
                                'enabled' => false,
                                'rules' => [
                                    [
                                        'value' => TicketSellingDeadline::EVENT_START_DATE,
                                        'actions' => [
                                            [
                                                'target' => $correctionContainerPath,
                                                'callback' => 'visible',
                                                'params' => [false]
                                            ]
                                        ]
                                    ],
                                    [
                                        'value' => TicketSellingDeadline::IN_ADVANCE,
                                        'actions' => [
                                            [
                                                'target' => $correctionContainerPath,
                                                'callback' => 'visible',
                                                'params' => [true]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $correctionFields = [
            DeadlineCorrectionInterface::DAYS => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Number::NAME,
                            'dataScope' => $scopePrefix . $correctionScopePrefix . DeadlineCorrectionInterface::DAYS,
                            'label' => __('Days'),
                            'validation' => [
                                'required-entry' => true,
                                'validate-integer' => true,
                                'validate-number-range' => '0-9999'
                            ]
                        ]
                    ]
                ]
            ],
            DeadlineCorrectionInterface::HOURS => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Number::NAME,
                            'dataScope' => $scopePrefix . $correctionScopePrefix . DeadlineCorrectionInterface::HOURS,
                            'label' => __('Hours'),
                            'validation' => [
                                'required-entry' => true,
                                'validate-integer' => true,
                                'validate-number-range' => '0-9999999'
                            ]
                        ]
                    ]
                ]
            ],
            DeadlineCorrectionInterface::MINUTES => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Number::NAME,
                            'dataScope' => $scopePrefix . $correctionScopePrefix . DeadlineCorrectionInterface::MINUTES,
                            'label' => __('Minutes'),
                            'validation' => [
                                'required-entry' => true,
                                'validate-integer' => true,
                                'validate-number-range' => '0-9999999'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $correctionFieldsContainer = $this->wrapFields(
            $correctionFields,
            self::CORRECTION_FIELDS_CONTAINER_NAME
        );

        return array_merge($fields, $correctionFieldsContainer);
    }

    /**
     * Wrap fields to container
     *
     * @param array $fields
     * @param string $containerName
     * @param string $template
     * @return array
     */
    private function wrapFields($fields, $containerName, $template = '')
    {
        $template = empty($template) ? 'Aheadworks_EventTickets/ui/form/components/group' : $template;

        $containerMeta = $this->arrayManager->set(
            $containerName . '/arguments/data/config',
            [],
            [
                'formElement' => 'container',
                'componentType' => 'container',
                'breakLine' => false,
                'component' => 'Aheadworks_EventTickets/js/ui/form/components/group',
                'template' => $template,
                'sortOrder' => $this->currentSortOrder
            ]
        );

        $this->currentSortOrder += 10;

        $containerMeta = $this->arrayManager->set(
            $containerName . '/children',
            $containerMeta,
            $fields
        );

        return $containerMeta;
    }
}
