<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Service;

use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Aheadworks\EventTickets\Model\Service\StockService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Source\Product\Stock\Status;
use Aheadworks\EventTickets\Model\Stock\Resolver\TicketSellingDeadlineDate as TicketSellingDeadlineResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class StockServiceTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Service
 */
class StockServiceTest extends TestCase
{
    /**
     * @var StockService
     */
    private $object;

    /**
     * @var ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * @var TicketSellingDeadlineResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketSellingDeadlineResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->productRepositoryMock = $this->getMockForAbstractClass(
            ProductRepositoryInterface::class
        );
        $this->localeDateMock = $this->getMockForAbstractClass(
            TimezoneInterface::class,
            [],
            '',
            true,
            true,
            true,
            [
                'scopeDate'
            ]
        );
        $this->ticketSellingDeadlineResolverMock = $this->createPartialMock(
            TicketSellingDeadlineResolver::class,
            [
                'resolve'
            ]
        );

        $this->object = $objectManager->getObject(
            StockService::class,
            [
                'productRepository' => $this->productRepositoryMock,
                'localeDate' => $this->localeDateMock,
                'ticketSellingDeadlineResolver' => $this->ticketSellingDeadlineResolverMock
            ]
        );
    }

    /**
     * Testing of getAvailableTicketQty method
     */
    public function testGetAvailableTicketQty()
    {
        $productId = 1;
        $expectedValue = 10;

        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQty'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQty')
            ->with($productMock)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getAvailableTicketQty($productId));
    }

    /**
     * Testing of getAvailableTicketQty method when corresponding product is not exist
     */
    public function testGetAvailableTicketQtyNoProduct()
    {
        $productId = 1;
        $expectedValue = 0;

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->getAvailableTicketQty($productId));
    }

    /**
     * Testing of getAvailableTicketQty method with non-ET product
     */
    public function testGetAvailableTicketQtyIncorrectProductType()
    {
        $productId = 1;
        $expectedValue = 0;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->getAvailableTicketQty($productId));
    }

    /**
     * Testing of getAvailableTicketQtyBySector method
     */
    public function testGetAvailableTicketQtyBySector()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = 10;

        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQtyBySector'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQtyBySector')
            ->with($productMock, $sectorId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getAvailableTicketQtyBySector($productId, $sectorId));
    }

    /**
     * Testing of getAvailableTicketQty method when corresponding product is not exist
     */
    public function testGetAvailableTicketQtyBySectorNoProduct()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = 0;

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->getAvailableTicketQtyBySector($productId, $sectorId));
    }

    /**
     * Testing of getAvailableTicketQty method with non-ET product
     */
    public function testGetAvailableTicketQtyBySectorIncorrectProductType()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = 0;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->getAvailableTicketQtyBySector($productId, $sectorId));
    }

    /**
     * Testing of getTicketSectorStatus method
     * @dataProvider getTicketSectorStatusDataProvider
     *
     * @param int $productId
     * @param int $sectorId
     * @param int $qty
     * @param bool $isFreeTicketsInSector
     * @param \DateTime $deadlineDate
     * @param \DateTime $nowDate
     * @param int $expectedValue
     */
    public function testGetTicketSectorStatus(
        $productId,
        $sectorId,
        $qty,
        $isFreeTicketsInSector,
        $deadlineDate,
        $nowDate,
        $expectedValue
    ) {
        $storeMock = $this->createMock(Store::class);
        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQtyBySector',
                'isFreeTicketsInSector'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance',
                'getStore'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQtyBySector')
            ->with($productMock, $sectorId)
            ->willReturn($qty);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('isFreeTicketsInSector')
            ->with($productMock, $sectorId)
            ->willReturn($isFreeTicketsInSector);

        $this->ticketSellingDeadlineResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($deadlineDate);

        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeMock, null, true)
            ->willReturn($nowDate);

        $this->assertEquals($expectedValue, $this->object->getTicketSectorStatus($productId, $sectorId));
    }

    /**
     * @return array
     */
    public function getTicketSectorStatusDataProvider()
    {
        return [
            [
                1,
                2,
                10,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::CAPACITY
            ],
            [
                1,
                2,
                10,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::AVAILABLE
            ],
            [
                1,
                2,
                0,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::FULL
            ],
            [
                1,
                2,
                0,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::SOLD_OUT
            ],
            [
                1,
                2,
                0,
                false,
                new \DateTime('2018-03-01'),
                new \DateTime('2018-03-02'),
                Status::UNAVAILABLE
            ],
        ];
    }

    /**
     * Testing of getTicketSectorStatus method when corresponding product is not exist
     */
    public function testGetTicketSectorStatusNoProduct()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = Status::UNAVAILABLE;

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->getTicketSectorStatus($productId, $sectorId));
    }

    /**
     * Testing of getTicketSectorStatus method with non-ET product
     */
    public function testGetTicketSectorStatusIncorrectProductType()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = Status::UNAVAILABLE;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->getTicketSectorStatus($productId, $sectorId));
    }

    /**
     * Testing of getTicketStatus method
     * @dataProvider getTicketStatusDataProvider
     *
     * @param int $productId
     * @param int $qty
     * @param bool $isFreeTicketsByProduct
     * @param \DateTime $deadlineDate
     * @param \DateTime $nowDate
     * @param int $expectedValue
     */
    public function testGetTicketStatus(
        $productId,
        $qty,
        $isFreeTicketsByProduct,
        $deadlineDate,
        $nowDate,
        $expectedValue
    ) {
        $storeMock = $this->createMock(Store::class);
        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQty',
                'isFreeTicketsByProduct'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance',
                'getStore'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQty')
            ->with($productMock)
            ->willReturn($qty);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('isFreeTicketsByProduct')
            ->with($productMock)
            ->willReturn($isFreeTicketsByProduct);

        $this->ticketSellingDeadlineResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($deadlineDate);

        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeMock, null, true)
            ->willReturn($nowDate);

        $this->assertEquals($expectedValue, $this->object->getTicketStatus($productId));
    }

    /**
     * @return array
     */
    public function getTicketStatusDataProvider()
    {
        return [
            [
                1,
                10,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::CAPACITY
            ],
            [
                1,
                10,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::AVAILABLE
            ],
            [
                1,
                0,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::FULL
            ],
            [
                1,
                0,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                Status::SOLD_OUT
            ],
            [
                1,
                0,
                false,
                new \DateTime('2018-03-01'),
                new \DateTime('2018-03-02'),
                Status::UNAVAILABLE
            ],
        ];
    }

    /**
     * Testing of getTicketStatus method when corresponding product is not exist
     */
    public function testGetTicketStatusNoProduct()
    {
        $productId = 1;
        $expectedValue = Status::UNAVAILABLE;

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->getTicketStatus($productId));
    }

    /**
     * Testing of getTicketStatus method with non-ET product
     */
    public function testGetTicketStatusIncorrectProductType()
    {
        $productId = 1;
        $expectedValue = Status::UNAVAILABLE;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->getTicketStatus($productId));
    }

    /**
     * Testing of isAvailableTicketQtyBySector method
     */
    public function testIsAvailableTicketQtyBySectorTrue()
    {
        $qty = 3;
        $productId = 1;
        $sectorId = 2;
        $availableTicketQtyBySector = 10;
        $expectedValue = true;

        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQtyBySector'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQtyBySector')
            ->with($productMock, $sectorId)
            ->willReturn($availableTicketQtyBySector);

        $this->assertEquals($expectedValue, $this->object->isAvailableTicketQtyBySector($qty, $productId, $sectorId));
    }

    /**
     * Testing of isAvailableTicketQtyBySector method
     */
    public function testIsAvailableTicketQtyBySectorFalse()
    {
        $qty = 30;
        $productId = 1;
        $sectorId = 2;
        $availableTicketQtyBySector = 10;
        $expectedValue = false;

        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQtyBySector'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQtyBySector')
            ->with($productMock, $sectorId)
            ->willReturn($availableTicketQtyBySector);

        $this->assertEquals($expectedValue, $this->object->isAvailableTicketQtyBySector($qty, $productId, $sectorId));
    }

    /**
     * Testing of isAvailableTicketQtyBySector method when corresponding product is not exist
     */
    public function testIsAvailableTicketQtyBySectorNoProduct()
    {
        $qty = 30;
        $productId = 1;
        $sectorId = 2;
        $expectedValue = false;

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->isAvailableTicketQtyBySector($qty, $productId, $sectorId));
    }

    /**
     * Testing of isAvailableTicketQtyBySector method with non-ET product
     */
    public function testIsAvailableTicketQtyBySectorIncorrectProductType()
    {
        $qty = 30;
        $productId = 1;
        $sectorId = 2;
        $expectedValue = false;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->isAvailableTicketQtyBySector($qty, $productId, $sectorId));
    }

    /**
     * Testing of isSalable method
     * @dataProvider isSalableDataProvider
     *
     * @param int $productId
     * @param int $qty
     * @param bool $isFreeTicketsByProduct
     * @param \DateTime $deadlineDate
     * @param \DateTime $nowDate
     * @param bool $expectedValue
     */
    public function testIsSalable(
        $productId,
        $qty,
        $isFreeTicketsByProduct,
        $deadlineDate,
        $nowDate,
        $expectedValue
    ) {
        $storeMock = $this->createMock(Store::class);
        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQty',
                'isFreeTicketsByProduct'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance',
                'getStore'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQty')
            ->with($productMock)
            ->willReturn($qty);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('isFreeTicketsByProduct')
            ->with($productMock)
            ->willReturn($isFreeTicketsByProduct);

        $this->ticketSellingDeadlineResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($deadlineDate);

        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeMock, null, true)
            ->willReturn($nowDate);

        $this->assertEquals($expectedValue, $this->object->isSalable($productId));
    }

    /**
     * @return array
     */
    public function isSalableDataProvider()
    {
        return [
            [
                1,
                10,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                true
            ],
            [
                1,
                10,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                true
            ],
            [
                1,
                0,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                false
            ],
            [
                1,
                0,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                false
            ],
        ];
    }

    /**
     * Testing of isSalable method when corresponding product is not exist
     */
    public function testIsSalableNoProduct()
    {
        $productId = 1;
        $expectedValue = false;

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->isSalable($productId));
    }

    /**
     * Testing of isSalable method with non-ET product
     */
    public function testIsSalableIncorrectProductType()
    {
        $productId = 1;
        $expectedValue = false;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->isSalable($productId));
    }

    /**
     * Testing of isSalableBySector method
     * @dataProvider isSalableBySectorDataProvider
     *
     * @param int $productId
     * @param int $sectorId
     * @param int $qty
     * @param bool $isFreeTicketsInSector
     * @param \DateTime $deadlineDate
     * @param \DateTime $nowDate
     * @param int $expectedValue
     */
    public function testIsSalableBySector(
        $productId,
        $sectorId,
        $qty,
        $isFreeTicketsInSector,
        $deadlineDate,
        $nowDate,
        $expectedValue
    ) {
        $storeMock = $this->createMock(Store::class);
        $eventTicketProductTypeMock = $this->createPartialMock(
            EventTicket::class,
            [
                'getAvailableTicketQtyBySector',
                'isFreeTicketsInSector'
            ]
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance',
                'getStore'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('getAvailableTicketQtyBySector')
            ->with($productMock, $sectorId)
            ->willReturn($qty);

        $eventTicketProductTypeMock->expects($this->once())
            ->method('isFreeTicketsInSector')
            ->with($productMock, $sectorId)
            ->willReturn($isFreeTicketsInSector);

        $this->ticketSellingDeadlineResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($deadlineDate);

        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeMock, null, true)
            ->willReturn($nowDate);

        $this->assertEquals($expectedValue, $this->object->isSalableBySector($productId, $sectorId));
    }

    /**
     * @return array
     */
    public function isSalableBySectorDataProvider()
    {
        return [
            [
                1,
                2,
                10,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                true
            ],
            [
                1,
                2,
                10,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                true
            ],
            [
                1,
                2,
                0,
                true,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                false
            ],
            [
                1,
                2,
                0,
                false,
                new \DateTime('2018-03-03'),
                new \DateTime('2018-03-02'),
                false
            ],
        ];
    }

    /**
     * Testing of isSalableBySector method when corresponding product is not exist
     */
    public function testIsSalableBySectorNoProduct()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = false;

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->isSalableBySector($productId, $sectorId));
    }

    /**
     * Testing of isSalableBySector method with non-ET product
     */
    public function testIsSalableBySectorIncorrectProductType()
    {
        $productId = 1;
        $sectorId = 2;
        $expectedValue = false;

        $simpleProductTypeMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Type\Simple::class,
            []
        );

        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance'
            ]
        );
        $productMock->expects($this->exactly(3))
            ->method('getTypeInstance')
            ->willReturn($simpleProductTypeMock);

        $this->productRepositoryMock->expects($this->exactly(3))
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($expectedValue, $this->object->isSalableBySector($productId, $sectorId));
    }

    /**
     * Testing of isTicketSellingDeadline method when corresponding product is not exist
     */
    public function testIsTicketSellingDeadlineNoProduct()
    {
        $productId = 1;
        $expectedValue = true;

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($expectedValue, $this->object->isTicketSellingDeadline($productId));
    }

    /**
     * Testing of isTicketSellingDeadline method
     */
    public function testIsTicketSellingDeadline()
    {
        $productId = 1;
        $storeId = 2;
        $deadlineDate = new \DateTime("12-12-2018 00:00");
        $currentFormattedDate = "01-01-2018 00:00";
        $expectedValue = false;

        $eventTicketProductTypeMock = $this->createMock(EventTicket::class);
        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance',
                'getStore'
            ]
        );

        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->ticketSellingDeadlineResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($deadlineDate);

        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeId, null, true)
            ->willReturn(new \DateTime($currentFormattedDate));

        $this->assertEquals($expectedValue, $this->object->isTicketSellingDeadline($productId));
    }

    /**
     * Testing of isTicketSellingDeadline method
     */
    public function testIsTicketSellingDeadlineTrue()
    {
        $productId = 1;
        $storeId = 2;
        $deadlineDate = new \DateTime("12-12-2018 00:00");
        $currentFormattedDate = "01-01-2020 00:00";
        $expectedValue = true;

        $eventTicketProductTypeMock = $this->createMock(EventTicket::class);
        $productMock = $this->createPartialMock(
            Product::class,
            [
                'getTypeInstance',
                'getStore'
            ]
        );

        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($eventTicketProductTypeMock);

        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->ticketSellingDeadlineResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($deadlineDate);

        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeId, null, true)
            ->willReturn(new \DateTime($currentFormattedDate));

        $this->assertEquals($expectedValue, $this->object->isTicketSellingDeadline($productId));
    }
}
