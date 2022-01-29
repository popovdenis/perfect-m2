<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Ticket\Creator;
use Magento\Catalog\Model\Product\Type;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem as GenerateFromOrderItem;

/**
 * Class CreatorTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket
 */
class CreatorTest extends TestCase
{
    /**
     * @var Creator
     */
    private $model;

    /**
     * @var GenerateFromOrderItem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $generateFromOrderItemMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->generateFromOrderItemMock = $this->createPartialMock(GenerateFromOrderItem::class, ['generateTickets']);
        $this->model = $objectManager->getObject(
            Creator::class,
            [
                'generateFromOrderItem' => $this->generateFromOrderItemMock
            ]
        );
    }

    /**
     * Test createByOrder method without ticket products
     */
    public function testCreateByOrderWithoutTicketProducts()
    {
        $expected = [];
        $orderItemMock = $this->getMockForAbstractClass(OrderItemInterface::class);
        $orderMock = $this->createPartialMock(Order::class, ['getAllItems']);
        $orderMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$orderItemMock]);
        $orderItemMock->expects($this->once())
            ->method('getProductType')
            ->willReturn(Type::TYPE_SIMPLE);

        $this->assertEquals($expected, $this->model->createByOrder($orderMock));
    }

    /**
     * Test createByOrder method with ticket products
     */
    public function testCreateByOrderWithTicketProducts()
    {
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $expected = [$ticketMock];
        $orderItemMock = $this->getMockForAbstractClass(OrderItemInterface::class);
        $orderMock = $this->createPartialMock(Order::class, ['getAllItems']);
        $orderMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$orderItemMock]);
        $orderItemMock->expects($this->once())
            ->method('getProductType')
            ->willReturn(EventTicket::TYPE_CODE);
        $this->generateFromOrderItemMock->expects($this->once())
            ->method('generateTickets')
            ->with($orderItemMock)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->createByOrder($orderMock));
    }
}
