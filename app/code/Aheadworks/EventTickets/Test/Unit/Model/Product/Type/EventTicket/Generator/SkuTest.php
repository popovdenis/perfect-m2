<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Product\Type\EventTicket\Generator;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Generator\Sku;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface as ProductConfigurationItemOptionInterface;

/**
 * Class SkuTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Product\Type\EventTicket\Generator
 */
class SkuTest extends TestCase
{
    /**
     * @var Sku
     */
    private $model;

    /**
     * @var SectorRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sectorRepositoryMock;

    /**
     * @var TicketTypeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketTypeRepositoryMock;

    /**
     * @var Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productMock;

    /**
     * @var int
     */
    private $productStoreId = 1;

    /**
     * @var int
     */
    private $ticketTypeId = 1;

    /**
     * @var int
     */
    private $sectorId = 1;

    /**
     * @var array
     */
    private $sku = [
        'product_sku' => 'sku',
        'sector_sku' => 'sector-sku',
        'ticket_type_sku' => 'ticket-type-sku',
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->sectorRepositoryMock = $this->getMockForAbstractClass(SectorRepositoryInterface::class);
        $this->ticketTypeRepositoryMock = $this->getMockForAbstractClass(TicketTypeRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Sku::class,
            [
                'sectorRepository' => $this->sectorRepositoryMock,
                'ticketTypeRepository' => $this->ticketTypeRepositoryMock
            ]
        );
    }

    /**
     * Test generate method
     */
    public function testGenerate()
    {
        $expected = implode('-', [$this->sku['product_sku'], $this->sku['sector_sku'], $this->sku['ticket_type_sku']]);
        $this->init();

        $sectorMock = $this->getMockForAbstractClass(SectorInterface::class);
        $this->sectorRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->sectorId, $this->productStoreId)
            ->willReturn($sectorMock);
        $sectorMock->expects($this->once())
            ->method('getSku')
            ->willReturn($this->sku['sector_sku']);

        $ticketTypeMock = $this->getMockForAbstractClass(TicketTypeInterface::class);
        $this->ticketTypeRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->ticketTypeId, $this->productStoreId)
            ->willReturn($ticketTypeMock);
        $ticketTypeMock->expects($this->once())
            ->method('getSku')
            ->willReturn($this->sku['ticket_type_sku']);

        $this->assertEquals($expected, $this->model->generate($this->sku['product_sku'], $this->productMock));
    }

    /**
     * Test generate method
     */
    public function testGenerateWithoutSector()
    {
        $expected = implode('-', [$this->sku['product_sku'], $this->sku['ticket_type_sku']]);
        $exception = new NoSuchEntityException();
        $this->init();

        $this->sectorRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->sectorId, $this->productStoreId)
            ->willThrowException($exception);

        $ticketTypeMock = $this->getMockForAbstractClass(TicketTypeInterface::class);
        $this->ticketTypeRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->ticketTypeId, $this->productStoreId)
            ->willReturn($ticketTypeMock);
        $ticketTypeMock->expects($this->once())
            ->method('getSku')
            ->willReturn($this->sku['ticket_type_sku']);

        $this->assertEquals($expected, $this->model->generate($this->sku['product_sku'], $this->productMock));
    }

    /**
     * Test generate method
     */
    public function testGenerateWithoutTicketType()
    {
        $expected = implode('-', [$this->sku['product_sku'], $this->sku['sector_sku']]);
        $exception = new NoSuchEntityException();
        $this->init();

        $sectorMock = $this->getMockForAbstractClass(SectorInterface::class);
        $this->sectorRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->sectorId, $this->productStoreId)
            ->willReturn($sectorMock);
        $sectorMock->expects($this->once())
            ->method('getSku')
            ->willReturn($this->sku['sector_sku']);

        $this->ticketTypeRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->ticketTypeId, $this->productStoreId)
            ->willThrowException($exception);

        $this->assertEquals($expected, $this->model->generate($this->sku['product_sku'], $this->productMock));
    }

    /**
     * Init for test
     */
    private function init()
    {
        $this->productMock = $this->createPartialMock(
            Product::class,
            ['hasCustomOptions', 'getCustomOption', 'getStoreId']
        );
        $sectorIdOption = $this->getMockForAbstractClass(ProductConfigurationItemOptionInterface::class);
        $ticketTypeIdOption = $this->getMockForAbstractClass(ProductConfigurationItemOptionInterface::class);

        $this->productMock->expects($this->once())
            ->method('hasCustomOptions')
            ->willReturn(true);
        $this->productMock->expects($this->exactly(2))
            ->method('getStoreId')
            ->willReturn($this->productStoreId);

        $this->productMock->expects($this->exactly(2))
            ->method('getCustomOption')
            ->withConsecutive(
                [OptionInterface::SECTOR_ID],
                [OptionInterface::TICKET_TYPE_ID]
            )->willReturnOnConsecutiveCalls($sectorIdOption, $ticketTypeIdOption);

        $sectorIdOption->expects($this->once())
            ->method('getValue')
            ->willReturn($this->sectorId);

        $ticketTypeIdOption->expects($this->once())
            ->method('getValue')
            ->willReturn($this->ticketTypeId);
    }
}
