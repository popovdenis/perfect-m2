<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Processor;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Processor\FromOrder as FromOrderProcessor;
use Aheadworks\EventTickets\Model\Config;

/**
 * Class ProcessorTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket
 */
class ProcessorTest extends TestCase
{
    /**
     * @var Processor
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var FromOrderProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fromOrderProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->createPartialMock(Config::class, ['getOrderStatusForTicketCreation']);
        $this->fromOrderProcessorMock = $this->createPartialMock(
            FromOrderProcessor::class,
            ['getTicketsToActivateByOrderStatus', 'getTicketsToActivateByOrderInvoices']
        );
        $this->model = $objectManager->getObject(
            Processor::class,
            [
                'config' => $this->configMock,
                'fromOrderProcessor' => $this->fromOrderProcessorMock
            ]
        );
    }

    /**
     * Test getPendingTicketsToActivateByOrder method
     *
     * @param string $orderStatus
     * @param string $configStatus
     * @dataProvider orderStatusDataProvider
     */
    public function testGetPendingTicketsToActivateByOrder($orderStatus, $configStatus)
    {
        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $expected = [$ticketMock];

        $orderMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($orderStatus);
        $this->configMock->expects($this->once())
            ->method('getOrderStatusForTicketCreation')
            ->willReturn($configStatus);

        $method = 'getTicketsToActivateByOrderInvoices';
        if ($orderStatus == $configStatus) {
            $method = 'getTicketsToActivateByOrderStatus';
        }

        $this->fromOrderProcessorMock->expects($this->once())
            ->method($method)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getPendingTicketsToActivateByOrder($orderMock));
    }

    /**
     * Test getPendingTicketsToActivateByOrder method on exception
     *
     * @param string $orderStatus
     * @param string $configStatus
     * @dataProvider orderStatusDataProvider
     * @expectedException \Exception
     */
    public function testGetPendingTicketsToActivateByOrderOnException($orderStatus, $configStatus)
    {
        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);
        $exception = new \Exception();

        $orderMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($orderStatus);
        $this->configMock->expects($this->once())
            ->method('getOrderStatusForTicketCreation')
            ->willReturn($configStatus);

        $method = 'getTicketsToActivateByOrderInvoices';
        if ($orderStatus == $configStatus) {
            $method = 'getTicketsToActivateByOrderStatus';
        }

        $this->fromOrderProcessorMock->expects($this->once())
            ->method($method)
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->model->getPendingTicketsToActivateByOrder($orderMock);
    }

    /**
     * Data provider for decrypt test
     *
     * @return array
     */
    public function orderStatusDataProvider()
    {
        return [['complete', 'complete'], ['holded', 'complete'], ['closed', 'complete']];
    }
}
