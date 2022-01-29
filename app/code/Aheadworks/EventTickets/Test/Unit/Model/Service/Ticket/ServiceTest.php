<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Service\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Service\Ticket\Service;
use Aheadworks\EventTickets\Model\Ticket\Creator as TicketCreator;
use Aheadworks\EventTickets\Model\Ticket\Processor as TicketProcessor;
use Aheadworks\EventTickets\Api\TicketActionManagementInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class ServiceTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Service\Ticket
 */
class ServiceTest extends TestCase
{
    /**
     * @var Service|\PHPUnit_Framework_MockObject_MockObject
     */
    private $object;

    /**
     * @var TicketCreator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketCreatorMock;

    /**
     * @var TicketProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketProcessorMock;

    /**
     * @var TicketActionManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketActionServiceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);

        $this->ticketCreatorMock = $this->createPartialMock(
            TicketCreator::class,
            [
                'createByOrder'
            ]
        );

        $this->ticketProcessorMock = $this->createPartialMock(
            TicketProcessor::class,
            [
                'getPendingTicketsToActivateByOrder'
            ]
        );

        $this->ticketActionServiceMock = $this->getMockForAbstractClass(
            TicketActionManagementInterface::class,
            [],
            '',
            false,
            true,
            true,
            [
                'doAction'
            ]
        );

        $this->object = $objectManager->getObject(
            Service::class,
            [
                'ticketCreator' => $this->ticketCreatorMock,
                'ticketProcessor' => $this->ticketProcessorMock,
                'ticketActionService' => $this->ticketActionServiceMock
            ]
        );
    }

    /**
     * Testing of processOrderSaving method
     */
    public function testProcessOrderSaving()
    {
        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);

        $this->ticketCreatorMock->expects($this->once())
            ->method('createByOrder')
            ->with($orderMock);

        $pendingTicketsToActivate = [
            $this->getMockForAbstractClass(TicketInterface::class)
        ];

        $this->ticketProcessorMock->expects($this->once())
            ->method('getPendingTicketsToActivateByOrder')
            ->with($orderMock)
            ->willReturn($pendingTicketsToActivate);

        $activatedTickets = [
            $this->getMockForAbstractClass(TicketInterface::class)
        ];

        $this->ticketActionServiceMock->expects($this->once())
            ->method('doAction')
            ->with(TicketInterface::ACTIVATE_ACTION_NAME, $pendingTicketsToActivate)
            ->willReturn($activatedTickets);

        $this->object->processOrderSaving($orderMock);
    }

    /**
     * Testing of activatePendingTicketsByOrder method
     */
    public function testActivatePendingTicketsByOrder()
    {
        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);

        $pendingTicketsToActivate = [
            $this->getMockForAbstractClass(TicketInterface::class)
        ];

        $this->ticketProcessorMock->expects($this->once())
            ->method('getPendingTicketsToActivateByOrder')
            ->with($orderMock)
            ->willReturn($pendingTicketsToActivate);

        $activatedTickets = [
            $this->getMockForAbstractClass(TicketInterface::class)
        ];

        $this->ticketActionServiceMock->expects($this->once())
            ->method('doAction')
            ->with(TicketInterface::ACTIVATE_ACTION_NAME, $pendingTicketsToActivate)
            ->willReturn($activatedTickets);

        $this->assertTrue(is_array($this->object->activatePendingTicketsByOrder($orderMock)));
    }
}
