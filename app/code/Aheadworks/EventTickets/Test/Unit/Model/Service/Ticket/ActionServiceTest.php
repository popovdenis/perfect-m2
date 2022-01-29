<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Service\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Service\Ticket\ActionService;
use Aheadworks\EventTickets\Model\Ticket\Action\ActionPool;
use Aheadworks\EventTickets\Model\Ticket\Action\TicketResolver;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\AbstractAction;

/**
 * Class ActionServiceTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Service\Ticket
 */
class ActionServiceTest extends TestCase
{
    /**
     * @var ActionService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $object;

    /**
     * @var ActionPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $actionPoolMock;

    /**
     * @var TicketResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);

        $this->actionPoolMock = $this->createPartialMock(
            ActionPool::class,
            [
                'getAction'
            ]
        );

        $this->ticketResolverMock = $this->createPartialMock(
            TicketResolver::class,
            [
                'getResolvedTicketsArray'
            ]
        );

        $this->object = $objectManager->getObject(
            ActionService::class,
            [
                'actionPool' => $this->actionPoolMock,
                'ticketResolver' => $this->ticketResolverMock
            ]
        );
    }

    /**
     * Testing of doAction method with wrong action name
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown ticket action metadata: wrong_action_name requested
     */
    public function testDoActionWrongActionName()
    {
        $actionName = 'wrong_action_name';
        $ticketsArray = ['1', '2'];
        $additionalData = [];

        $this->actionPoolMock->expects($this->once())
            ->method('getAction')
            ->with($actionName)
            ->willThrowException(new \Exception('Unknown ticket action metadata: wrong_action_name requested'));

        $this->expectException(\Exception::class);
        $this->object->doAction($actionName, $ticketsArray, $additionalData);
    }

    /**
     * Testing of doAction method
     */
    public function testDoAction()
    {
        $actionName = TicketInterface::CHECK_IN_ACTION_NAME;
        $ticketsArray = ['1', '2'];
        $additionalData = [];
        $resolvedTicketsArray = [
            $this->getMockForAbstractClass(TicketInterface::class),
            $this->getMockForAbstractClass(TicketInterface::class)
        ];

        $expectedResult = [
            $this->getMockForAbstractClass(TicketInterface::class)
        ];

        $actionMock = $this->getMockForAbstractClass(
            AbstractAction::class,
            [],
            '',
            false,
            true,
            true,
            [
                'execute'
            ]
        );
        $actionMock->expects($this->once())
            ->method('execute')
            ->with($resolvedTicketsArray, $additionalData)
            ->willReturn($expectedResult);

        $this->actionPoolMock->expects($this->once())
            ->method('getAction')
            ->with($actionName)
            ->willReturn($actionMock);

        $this->ticketResolverMock->expects($this->once())
            ->method('getResolvedTicketsArray')
            ->with($ticketsArray)
            ->willReturn($resolvedTicketsArray);

        $this->assertTrue(is_array($this->object->doAction($actionName, $ticketsArray, $additionalData)));
    }
}
