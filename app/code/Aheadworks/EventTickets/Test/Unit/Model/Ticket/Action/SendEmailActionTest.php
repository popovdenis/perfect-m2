<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Action\SendEmailAction;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;
use Aheadworks\EventTickets\Model\Ticket\Notifier\Grouping as TicketGrouping;
use Aheadworks\EventTickets\Model\Ticket\Notifier as TicketNotifier;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Model\Source\Email\Status as EmailStatus;

/**
 * Class SendEmailActionTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action
 */
class SendEmailActionTest extends TestCase
{
    /**
     * @var SendEmailAction
     */
    private $object;

    /**
     * @var TicketRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketRepositoryMock;

    /**
     * @var TicketGrouping|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketGroupingMock;

    /**
     * @var TicketNotifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketNotifierMock;

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
        $this->ticketGroupingMock = $this->createPartialMock(
            TicketGrouping::class,
            [
                'process'
            ]
        );
        $this->ticketNotifierMock = $this->createPartialMock(
            TicketNotifier::class,
            [
                'processActivatedTicketGroup'
            ]
        );

        $this->object = $objectManager->getObject(
            SendEmailAction::class,
            [
                'ticketStatusResolver' => $this->ticketStatusResolverMock,
                'ticketGrouping' => $this->ticketGroupingMock,
                'ticketNotifier' => $this->ticketNotifierMock,
                'ticketRepository' => $this->ticketRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecuteEmptyArray()
    {
        $this->ticketGroupingMock->expects($this->once())
            ->method('process')
            ->with([])
            ->willReturn([]);

        $this->ticketNotifierMock->expects($this->never())
            ->method('processActivatedTicketGroup');

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
        $unavailableForActionTicket = $this->getTicketMockUnavailableForSendEmailAction();
        $this->ticketStatusResolverMock->expects($this->never())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ]
                ]
            )
        ;

        $this->ticketGroupingMock->expects($this->once())
            ->method('process')
            ->with([])
            ->willReturn([]);

        $this->ticketNotifierMock->expects($this->never())
            ->method('processActivatedTicketGroup');

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
    public function testExecuteSingleTicketSent()
    {
        $emailSentStatus = EmailStatus::SENT;
        $unavailableForActionTicket = $this->getTicketMockUnavailableForSendEmailAction();
        $ticketAvailableForAction = $this->getTicketMockForSendEmailAction($emailSentStatus);
        $groupedTickets = [
            '1' => [$ticketAvailableForAction]
        ];
        $isEmailSent = true;

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketGroupingMock->expects($this->once())
            ->method('process')
            ->with([$ticketAvailableForAction])
            ->willReturn($groupedTickets);

        $this->ticketNotifierMock->expects($this->exactly(count($groupedTickets)))
            ->method('processActivatedTicketGroup')
            ->with([$ticketAvailableForAction])
            ->willReturn($isEmailSent);

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
    public function testExecuteSingleTicketFailed()
    {
        $emailSentStatus = EmailStatus::FAILED;
        $unavailableForActionTicket = $this->getTicketMockUnavailableForSendEmailAction();
        $ticketAvailableForAction = $this->getTicketMockForSendEmailAction($emailSentStatus);
        $groupedTickets = [
            '1' => [$ticketAvailableForAction]
        ];
        $isEmailSent = false;

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketGroupingMock->expects($this->once())
            ->method('process')
            ->with([$ticketAvailableForAction])
            ->willReturn($groupedTickets);

        $this->ticketNotifierMock->expects($this->exactly(count($groupedTickets)))
            ->method('processActivatedTicketGroup')
            ->with([$ticketAvailableForAction])
            ->willReturn($isEmailSent);

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
    public function testExecuteFewTicketGroupDifferentStatuses()
    {
        $unavailableForActionTicket = $this->getTicketMockUnavailableForSendEmailAction();

        $emailSentStatusFirstGroup = EmailStatus::SENT;
        $emailSentStatusSecondGroup = EmailStatus::FAILED;
        $ticketAvailableForActionFirstGroup = $this->getTicketMockForSendEmailAction($emailSentStatusFirstGroup);
        $ticketAvailableForActionSecondGroup = $this->getTicketMockForSendEmailAction($emailSentStatusSecondGroup);
        $groupedTickets = [
            '1' => [$ticketAvailableForActionFirstGroup],
            '2' => [$ticketAvailableForActionSecondGroup]
        ];
        $isEmailSentFirstGroup = true;
        $isEmailSentSecondGroup = false;

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $ticketAvailableForActionFirstGroup,
                        true
                    ],
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $ticketAvailableForActionSecondGroup,
                        true
                    ]
                ]
            );

        $this->ticketGroupingMock->expects($this->once())
            ->method('process')
            ->with([$ticketAvailableForActionFirstGroup, $ticketAvailableForActionSecondGroup])
            ->willReturn($groupedTickets);

        $this->ticketNotifierMock->expects($this->exactly(2))
            ->method('processActivatedTicketGroup')
            ->withConsecutive(
                [[$ticketAvailableForActionFirstGroup]],
                [[$ticketAvailableForActionSecondGroup]]
            )->willReturnOnConsecutiveCalls(
                $isEmailSentFirstGroup,
                $isEmailSentSecondGroup
            );

        $this->ticketRepositoryMock->expects($this->exactly(2))
            ->method('save');

        $this->assertTrue(is_array(
            $this->object->execute(
                [
                    $unavailableForActionTicket,
                    $ticketAvailableForActionFirstGroup,
                    $ticketAvailableForActionSecondGroup
                ]
            )
        ));
    }

    /**
     * Testing of execute method
     */
    public function testExecuteSingleTicketFailedErrorInRepository()
    {
        $emailSentStatus = EmailStatus::FAILED;
        $unavailableForActionTicket = $this->getTicketMockUnavailableForSendEmailAction();
        $ticketAvailableForAction = $this->getTicketMockForSendEmailAction($emailSentStatus);
        $groupedTickets = [
            '1' => [$ticketAvailableForAction]
        ];
        $isEmailSent = false;

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::SEND_EMAIL_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

        $this->ticketGroupingMock->expects($this->once())
            ->method('process')
            ->with([$ticketAvailableForAction])
            ->willReturn($groupedTickets);

        $this->ticketNotifierMock->expects($this->exactly(count($groupedTickets)))
            ->method('processActivatedTicketGroup')
            ->with([$ticketAvailableForAction])
            ->willReturn($isEmailSent);

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
     * Retrieve ticket mock for send email action
     *
     * @param int $emailSentStatus
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockForSendEmailAction($emailSentStatus)
    {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );
        $ticketMock->expects($this->atLeastOnce())
            ->method('setEmailSent')
            ->with($emailSentStatus)
            ->willReturnSelf();

        return $ticketMock;
    }

    /**
     * Retrieve ticket mock unavailable for send email action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockUnavailableForSendEmailAction()
    {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );

        return $ticketMock;
    }
}
