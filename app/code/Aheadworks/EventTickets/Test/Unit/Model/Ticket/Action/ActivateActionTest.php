<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Action\ActivateAction;
use Aheadworks\EventTickets\Model\Ticket\Action\SendEmailAction;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ActivateActionTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action
 */
class ActivateActionTest extends TestCase
{
    /**
     * @var ActivateAction
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
     * @var SendEmailAction|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sendEmailActionMock;

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
        $this->sendEmailActionMock = $this->createPartialMock(
            SendEmailAction::class,
            [
                'execute'
            ]
        );

        $this->object = $objectManager->getObject(
            ActivateAction::class,
            [
                'ticketStatusResolver' => $this->ticketStatusResolverMock,
                'ticketRepository' => $this->ticketRepositoryMock,
                'sendEmailAction' => $this->sendEmailActionMock,
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $unavailableForActionTicket = $this->getTicketMockUnavailableForActivateAction();
        $ticketAvailableForAction = $this->getTicketMockForActivateAction();

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::ACTIVATE_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::ACTIVATE_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketAvailableForAction)
            ->willReturn($ticketAvailableForAction);

        $this->sendEmailActionMock->expects($this->once())
            ->method('execute')
            ->with([$ticketAvailableForAction])
            ->willReturn([$ticketAvailableForAction]);

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
        $unavailableForActionTicket = $this->getTicketMockUnavailableForActivateAction();
        $ticketAvailableForAction = $this->getTicketMockForActivateAction();

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::ACTIVATE_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::ACTIVATE_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketRepositoryMock->expects($this->once())
            ->method('save')
            ->with($ticketAvailableForAction)
            ->willThrowException(new LocalizedException(__('LocalizedException')));

        $this->sendEmailActionMock->expects($this->once())
            ->method('execute')
            ->with([])
            ->willReturn([]);

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

        $this->sendEmailActionMock->expects($this->once())
            ->method('execute')
            ->with([])
            ->willReturn([]);

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
        $unavailableForActionTicket = $this->getTicketMockUnavailableForActivateAction();
        $this->ticketStatusResolverMock->expects($this->never())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::ACTIVATE_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ]
                ]
            )
        ;

        $this->ticketRepositoryMock->expects($this->never())
            ->method('save')
        ;

        $this->sendEmailActionMock->expects($this->once())
            ->method('execute')
            ->with([])
            ->willReturn([]);

        $this->assertTrue(is_array(
            $this->object->execute(
                []
            )
        ));
    }

    /**
     * Retrieve ticket mock for activate action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockForActivateAction()
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
     * Retrieve ticket mock unavailable for activate action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockUnavailableForActivateAction()
    {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );

        return $ticketMock;
    }
}
