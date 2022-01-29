<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Action\UndoCheckInAction;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UndoCheckInActionTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action
 */
class UndoCheckInActionTest extends TestCase
{
    /**
     * @var UndoCheckInAction
     */
    private $object;

    /**
     * @var TicketRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketRepositoryMock;

    /**
     * @var TicketStatusResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketStatusResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketRepositoryMock = $this->getMockForAbstractClass(
            TicketRepositoryInterface::class
        );
        $this->ticketStatusResolverMock = $this->createPartialMock(
            TicketStatusResolver::class,
            [
                'isActionAllowedForTicket'
            ]
        );

        $this->object = $objectManager->getObject(
            UndoCheckInAction::class,
            [
                'ticketStatusResolver' => $this->ticketStatusResolverMock,
                'ticketRepository' => $this->ticketRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $unavailableForActionTicket = $this->getTicketMockUnavailableForUndoCheckInAction();
        $ticketAvailableForAction = $this->getTicketMockForUndoCheckInAction();

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketAvailableForAction)
            ->willReturn($ticketAvailableForAction);

        $this->assertTrue(is_array(
            $this->object->execute(
                [
                    $unavailableForActionTicket,
                    $ticketAvailableForAction
                ]
            )
        ));
    }

    /**
     * Testing of execute method
     */
    public function testExecuteErrorInRepository()
    {
        $unavailableForActionTicket = $this->getTicketMockUnavailableForUndoCheckInAction();
        $ticketAvailableForAction = $this->getTicketMockForUndoCheckInAction();

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketAvailableForAction)
            ->willThrowException(new LocalizedException(__('LocalizedException')));

        $this->assertTrue(is_array(
            $this->object->execute(
                [
                    $unavailableForActionTicket,
                    $ticketAvailableForAction
                ]
            )
        ));
    }

    /**
     * Testing of execute method
     */
    public function testExecuteEmptyArray()
    {
        $this->ticketStatusResolverMock->expects($this->never())
            ->method('isActionAllowedForTicket')
        ;

        $this->ticketRepositoryMock->expects($this->never())
            ->method('save')
        ;

        $this->assertTrue(is_array(
            $this->object->execute(
                []
            )
        ));
    }

    /**
     * Testing of execute method
     */
    public function testExecuteEmptyAvailableTicketsArray()
    {
        $unavailableForActionTicket = $this->getTicketMockUnavailableForUndoCheckInAction();
        $this->ticketStatusResolverMock->expects($this->never())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ]
                ]
            )
        ;

        $this->ticketRepositoryMock->expects($this->never())
            ->method('save')
        ;

        $this->assertTrue(is_array(
            $this->object->execute(
                []
            )
        ));
    }

    /**
     * Retrieve ticket mock for undo check in action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockForUndoCheckInAction()
    {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );
        $ticketMock->expects($this->once())
            ->method('setStatus')
            ->with(TicketStatus::UNUSED)
            ->willReturnSelf();

        return $ticketMock;
    }

    /**
     * Retrieve ticket mock unavailable for undo check in action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockUnavailableForUndoCheckInAction()
    {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );

        return $ticketMock;
    }
}
