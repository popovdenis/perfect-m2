<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Export;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Export\RowCustomizer;
use Magento\Eav\Model\Entity\Collection\AbstractCollection as AbstractEavCollection;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Base as BaseFormatter;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\DateTime as DateTimeFormatter;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Venue as VenueFormatter;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Space as SpaceFormatter;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline as TicketSellingDeadlineSource;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig\TicketType as SectorConfigTicketType;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig\Product as SectorConfigProduct;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions\Labels as PersonalOptionsLabels;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\PersonalOptions\Values as PersonalOptionsValues;

/**
 * Class RowCustomizerTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Export
 */
class RowCustomizerTest extends TestCase
{
    /**
     * @var RowCustomizer
     */
    private $model;

    /**
     * @var AttributeFormatterPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeFormatterPoolMock;

    /**
     * @var SectorConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sectorConfigCustomizerMock;

    /**
     * @var PersonalOptions|\PHPUnit_Framework_MockObject_MockObject
     */
    private $personalOptionsCustomizerMock;

    /**
     * @var array
     */
    private $eventTicketsAttributesToExport = [
        ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING,
        ProductAttributeInterface::CODE_AW_ET_START_DATE,
        ProductAttributeInterface::CODE_AW_ET_END_DATE,
        ProductAttributeInterface::CODE_AW_ET_VENUE_ID,
        ProductAttributeInterface::CODE_AW_ET_SPACE_ID,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE,
        ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE,
        ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE,
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS,
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);

        $this->attributeFormatterPoolMock = $this->createMock(AttributeFormatterPool::class);

        $this->sectorConfigCustomizerMock = $this->createMock(SectorConfig::class);
        $this->personalOptionsCustomizerMock = $this->createMock(PersonalOptions::class);

        $complexEventTicketsAttributesConfig = [
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG => $this->sectorConfigCustomizerMock,
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS => $this->personalOptionsCustomizerMock,
        ];

        $this->model = $objectManager->getObject(
            RowCustomizer::class,
            [
                'attributeFormatterPool' => $this->attributeFormatterPoolMock,
                'eventTicketsAttributesToExport' => $this->eventTicketsAttributesToExport,
                'complexEventTicketsAttributesConfig' => $complexEventTicketsAttributesConfig,
            ]
        );
    }

    /**
     * Test for prepareData function
     */
    public function testPrepareData()
    {
        $eventTicketProductId = 12;

        $eventVenueId = 1;
        $formattedEventVenue = 'Sample Venue 1';

        $eventSpaceId = 2;
        $formattedEventSpace = 'Sample Venue 1';

        $eventStartDate = '2019-03-01 08:52:00';
        $formattedEventStartDate = '3/1/19, 9:52 PM';

        $eventEndDate = '2019-03-31 08:51:00';
        $formattedEventEndDate = '3/31/19, 9:51 PM';

        $eventSellingDeadlineDate = '2019-02-28 00:00:00';
        $formattedEventSellingDeadlineDate = '2/28/19, 1:00 PM';

        $collectionMock = $this->getMockForAbstractClass(
            AbstractEavCollection::class,
            [],
            '',
            false,
            true,
            true,
            [
                'addAttributeToFilter',
                'addFieldToSelect',
                'load',
                'toArray',
            ]
        );
        $productIds = [
            $eventTicketProductId
        ];
        $eventTicketsProductsData = [
            $eventTicketProductId => [
                'entity_id' => $eventTicketProductId,
                'type_id' => 'aw_event_ticket',
                'sku' => 'AW test event 1',
                'name' => 'AW test event 1',
                ProductAttributeInterface::CODE_AW_ET_VENUE_ID => $eventVenueId,
                ProductAttributeInterface::CODE_AW_ET_SPACE_ID => $eventSpaceId,
                ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE =>
                    TicketSellingDeadlineSource::CUSTOM_DATE,
                ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE => $eventSellingDeadlineDate,
                ProductAttributeInterface::CODE_AW_ET_START_DATE => $eventStartDate,
                ProductAttributeInterface::CODE_AW_ET_END_DATE => $eventEndDate,
            ]
        ];

        $dateTimeFormatter = $this->createMock(DateTimeFormatter::class);
        $dateTimeFormatter->expects($this->any())
            ->method('getFormattedValue')
            ->willReturnMap(
                [
                    [
                        $eventSellingDeadlineDate,
                        $formattedEventSellingDeadlineDate
                    ],
                    [
                        $eventStartDate,
                        $formattedEventStartDate
                    ],
                    [
                        $eventEndDate,
                        $formattedEventEndDate
                    ]
                ]
            );
        $venueFormatter = $this->createMock(VenueFormatter::class);
        $venueFormatter->expects($this->once())
            ->method('getFormattedValue')
            ->with($eventVenueId)
            ->willReturn($formattedEventVenue);
        $spaceFormatter = $this->createMock(SpaceFormatter::class);
        $spaceFormatter->expects($this->once())
            ->method('getFormattedValue')
            ->with($eventSpaceId)
            ->willReturn($formattedEventSpace);
        $baseFormatter = $this->createMock(BaseFormatter::class);
        $baseFormatter->expects($this->any())
            ->method('getFormattedValue')
            ->willReturnMap(
                [
                    [
                        TicketSellingDeadlineSource::CUSTOM_DATE,
                        TicketSellingDeadlineSource::CUSTOM_DATE
                    ],
                ]
            );

        $this->attributeFormatterPoolMock->expects($this->any())
            ->method('getByAttributePath')
            ->willReturnMap(
                [
                    [
                        ProductAttributeInterface::CODE_AW_ET_VENUE_ID,
                        $venueFormatter
                    ],
                    [
                        ProductAttributeInterface::CODE_AW_ET_SPACE_ID,
                        $spaceFormatter
                    ],
                    [
                        ProductAttributeInterface::CODE_AW_ET_START_DATE,
                        $dateTimeFormatter
                    ],
                    [
                        ProductAttributeInterface::CODE_AW_ET_END_DATE,
                        $dateTimeFormatter
                    ],
                    [
                        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE,
                        $dateTimeFormatter
                    ],
                    [
                        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE,
                        $baseFormatter
                    ],
                ]
            );

        $collectionMock->expects($this->exactly(2))
            ->method('addAttributeToFilter')
            ->willReturnSelf();

        $countOfSimpleEventTicketsAttributes = count(
            array_diff(
                $this->eventTicketsAttributesToExport,
                [
                    ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
                    ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS,
                    ]
            )
        );
        $collectionMock->expects($this->exactly($countOfSimpleEventTicketsAttributes))
            ->method('addFieldToSelect')
            ->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('toArray')
            ->willReturn($eventTicketsProductsData);

        $simpleAttributesProductData = [
            ProductAttributeInterface::CODE_AW_ET_START_DATE => $formattedEventStartDate,
            ProductAttributeInterface::CODE_AW_ET_END_DATE => $formattedEventEndDate,
            ProductAttributeInterface::CODE_AW_ET_VENUE_ID => $formattedEventVenue,
            ProductAttributeInterface::CODE_AW_ET_SPACE_ID => $formattedEventSpace,
            ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE =>
                TicketSellingDeadlineSource::CUSTOM_DATE,
            ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE =>
                $formattedEventSellingDeadlineDate,
        ];

        $eventTicketsAttributesProductsData = [
            $eventTicketProductId => $simpleAttributesProductData
        ];

        $sectorConfigProductData = [
            SectorConfig::SECTOR_COLUMN_ID => "Regular\nPremium",
            SectorConfigTicketType::SECTOR_TICKET_TYPE_COLUMN_ID => "Standard|VIP|\nAdults|Kids|",
            SectorConfigTicketType::SECTOR_TICKET_TYPE_PRICE_COLUMN_ID => "10.0000|25.0000|\n5.0000|55.0000|",
            SectorConfigTicketType::SECTOR_TICKET_TYPE_POSITION_COLUMN_ID => "1|2|\n1|2|",
            SectorConfigTicketType::SECTOR_TICKET_TYPE_PERSONAL_OPTIONS_COLUMN_ID =>
                "Test 1,Test 2|Test 2|\nTest 2|Test 2|",
            SectorConfigProduct::SECTOR_PRODUCT_SKU_COLUMN_ID =>
                "24-MB01|24-MB04|24-MB03|\n24-WB02|24-WB05|24-WB06|24-WB03|",
            SectorConfigProduct::SECTOR_PRODUCT_POSITION_COLUMN_ID => "1|2|3|\n1|2|3|4|",
        ];

        $eventTicketsAttributesProductsDataWithSectorConfig = [
            $eventTicketProductId =>
                $simpleAttributesProductData
                + $sectorConfigProductData
        ];

        $this->sectorConfigCustomizerMock->expects($this->once())
            ->method('prepareData')
            ->with($eventTicketsAttributesProductsData)
            ->willReturn($eventTicketsAttributesProductsDataWithSectorConfig);

        $personalOptionsProductData = [
            PersonalOptions::OPTION_COLUMN_ID => "Test 1\nTest 2",
            PersonalOptions::OPTION_TYPE_COLUMN_ID => "name\ndropdown",
            PersonalOptions::OPTION_SORT_ORDER_COLUMN_ID => "1\n2",
            PersonalOptions::OPTION_IS_REQUIRED_COLUMN_ID => "1\n1",
            PersonalOptions::OPTION_IS_APPLIED_TO_ALL_TICKET_TYPES_COLUMN_ID => "\n1",
            PersonalOptionsLabels::OPTION_LABELS_STORE_COLUMN_ID =>
                "Admin|Default Store View|\nAdmin|Default Store View|",
            PersonalOptionsLabels::OPTION_LABELS_TITLE_COLUMN_ID =>
                "Test 1|Test 1 1|\nTest 2|test 2_2|",
            PersonalOptionsValues::OPTION_VALUES_COLUMN_ID => "\nVal1|Val2|Val3|",
            PersonalOptionsValues::OPTION_VALUES_SORT_ORDER_COLUMN_ID => "\n1|2|3|",
            PersonalOptionsValues::OPTION_VALUES_LABELS_STORE_COLUMN_ID =>
                "\nAdmin,Default Store View,AW test 2,|Admin,Default Store View,AW test 2,|Admin,|",
            PersonalOptionsValues::OPTION_VALUES_LABELS_TITLE_COLUMN_ID =>
                "\nVal1,Val1_1,Val1_2,|Val2,Val2_1,Val2_2,|Val3,|",
        ];

        $eventTicketsAttributesProductsDataWithSectorConfigAndPersonalOptions = [
            $eventTicketProductId =>
                $simpleAttributesProductData
                + $sectorConfigProductData
                + $personalOptionsProductData
        ];

        $this->personalOptionsCustomizerMock->expects($this->once())
            ->method('prepareData')
            ->with($eventTicketsAttributesProductsDataWithSectorConfig)
            ->willReturn($eventTicketsAttributesProductsDataWithSectorConfigAndPersonalOptions);

        $this->model->prepareData($collectionMock, $productIds);
    }
}
