<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Status;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver;
use Aheadworks\EventTickets\Model\Ticket\Status\RestrictionsPool;
use Aheadworks\EventTickets\Model\Ticket\Status\RestrictionsInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;

/**
 * Class ResolverTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Status
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $object;

    /**
     * @var RestrictionsPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $restrictionsPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->restrictionsPoolMock = $this->createPartialMock(
            RestrictionsPool::class,
            [
                'getRestrictions'
            ]
        );

        $this->object = $objectManager->getObject(
            Resolver::class,
            [
                'restrictionsPool' => $this->restrictionsPoolMock,
            ]
        );
    }

    /**
     * Testing of isActionAllowedForTicket method with exception in restrictionsPool
     */
    public function testIsActionAllowedForTicketException()
    {
        $actionName = TicketInterface::ACTIVATE_ACTION_NAME;
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );
        $ticketStatus = TicketStatus::PENDING;
        $ticketMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($ticketStatus);

        $this->restrictionsPoolMock->expects($this->once())
            ->method('getRestrictions')
            ->with($ticketStatus)
            ->willThrowException(new \Exception());

        $this->assertFalse($this->object->isActionAllowedForTicket($actionName, $ticketMock));
    }

    /**
     * Testing of isActionAllowedForTicketStatus method with exception in restrictionsPool
     */
    public function testIsActionAllowedForTicketStatusException()
    {
        $actionName = TicketInterface::ACTIVATE_ACTION_NAME;
        $ticketStatus = TicketStatus::PENDING;

        $this->restrictionsPoolMock->expects($this->once())
            ->method('getRestrictions')
            ->with($ticketStatus)
            ->willThrowException(new \Exception());

        $this->assertFalse($this->object->isActionAllowedForTicketStatus($actionName, $ticketStatus));
    }

    /**
     * Testing of isActionAllowedForTicket method
     * @dataProvider isActionAllowedForTicketDataProvider
     *
     * @param string $actionName
     * @param int $ticketStatus
     * @param array $allowedActionsNames
     * @param bool $expectedResult
     */
    public function testIsActionAllowedForTicket(
        $actionName,
        $ticketStatus,
        $allowedActionsNames,
        $expectedResult
    ) {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );
        $ticketMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($ticketStatus);

        $restrictionsMock = $this->getMockForAbstractClass(
            RestrictionsInterface::class
        );

        $restrictionsMock->expects($this->once())
            ->method('getAllowedActionsNames')
            ->willReturn($allowedActionsNames);

        $this->restrictionsPoolMock->expects($this->once())
            ->method('getRestrictions')
            ->with($ticketStatus)
            ->willReturn($restrictionsMock);

        $this->assertEquals($expectedResult, $this->object->isActionAllowedForTicket($actionName, $ticketMock));
    }

    /**
     * Testing of isActionAllowedForTicketStatus method
     * @dataProvider isActionAllowedForTicketDataProvider
     *
     * @param string $actionName
     * @param int $ticketStatus
     * @param array $allowedActionsNames
     * @param bool $expectedResult
     */
    public function testIsActionAllowedForTicketStatus(
        $actionName,
        $ticketStatus,
        $allowedActionsNames,
        $expectedResult
    ) {
        $restrictionsMock = $this->getMockForAbstractClass(
            RestrictionsInterface::class
        );

        $restrictionsMock->expects($this->once())
            ->method('getAllowedActionsNames')
            ->willReturn($allowedActionsNames);

        $this->restrictionsPoolMock->expects($this->once())
            ->method('getRestrictions')
            ->with($ticketStatus)
            ->willReturn($restrictionsMock);

        $this->assertEquals($expectedResult, $this->object->isActionAllowedForTicketStatus($actionName, $ticketStatus));
    }

    /**
     * @return array
     */
    public function isActionAllowedForTicketDataProvider()
    {
        return [
            [
                TicketInterface::ACTIVATE_ACTION_NAME,
                TicketStatus::UNUSED,
                [
                    TicketInterface::CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                    TicketInterface::DOWNLOAD_ACTION_NAME,
                    TicketInterface::SEND_EMAIL_ACTION_NAME,
                ],
                false
            ],
            [
                TicketInterface::ACTIVATE_ACTION_NAME,
                TicketStatus::USED,
                [
                    TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                ],
                false
            ],
            [
                TicketInterface::ACTIVATE_ACTION_NAME,
                TicketStatus::CANCELED,
                [],
                false
            ],
            [
                TicketInterface::ACTIVATE_ACTION_NAME,
                TicketStatus::PENDING,
                [
                    TicketInterface::ACTIVATE_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME
                ],
                true
            ],

            [
                TicketInterface::CANCEL_ACTION_NAME,
                TicketStatus::UNUSED,
                [
                    TicketInterface::CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                    TicketInterface::DOWNLOAD_ACTION_NAME,
                    TicketInterface::SEND_EMAIL_ACTION_NAME,
                ],
                true
            ],
            [
                TicketInterface::CANCEL_ACTION_NAME,
                TicketStatus::USED,
                [
                    TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                ],
                true
            ],
            [
                TicketInterface::CANCEL_ACTION_NAME,
                TicketStatus::CANCELED,
                [],
                false
            ],
            [
                TicketInterface::CANCEL_ACTION_NAME,
                TicketStatus::PENDING,
                [
                    TicketInterface::ACTIVATE_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME
                ],
                true
            ],

            [
                TicketInterface::CHECK_IN_ACTION_NAME,
                TicketStatus::UNUSED,
                [
                    TicketInterface::CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                    TicketInterface::DOWNLOAD_ACTION_NAME,
                    TicketInterface::SEND_EMAIL_ACTION_NAME,
                ],
                true
            ],
            [
                TicketInterface::CHECK_IN_ACTION_NAME,
                TicketStatus::USED,
                [
                    TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                ],
                false
            ],
            [
                TicketInterface::CHECK_IN_ACTION_NAME,
                TicketStatus::CANCELED,
                [],
                false
            ],
            [
                TicketInterface::CHECK_IN_ACTION_NAME,
                TicketStatus::PENDING,
                [
                    TicketInterface::ACTIVATE_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME
                ],
                false
            ],

            [
                TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                TicketStatus::UNUSED,
                [
                    TicketInterface::CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                    TicketInterface::DOWNLOAD_ACTION_NAME,
                    TicketInterface::SEND_EMAIL_ACTION_NAME,
                ],
                false
            ],
            [
                TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                TicketStatus::USED,
                [
                    TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                ],
                true
            ],
            [
                TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                TicketStatus::CANCELED,
                [],
                false
            ],
            [
                TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                TicketStatus::PENDING,
                [
                    TicketInterface::ACTIVATE_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME
                ],
                false
            ],

            [
                TicketInterface::SEND_EMAIL_ACTION_NAME,
                TicketStatus::UNUSED,
                [
                    TicketInterface::CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                    TicketInterface::DOWNLOAD_ACTION_NAME,
                    TicketInterface::SEND_EMAIL_ACTION_NAME,
                ],
                true
            ],
            [
                TicketInterface::SEND_EMAIL_ACTION_NAME,
                TicketStatus::USED,
                [
                    TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                ],
                false
            ],
            [
                TicketInterface::SEND_EMAIL_ACTION_NAME,
                TicketStatus::CANCELED,
                [],
                false
            ],
            [
                TicketInterface::SEND_EMAIL_ACTION_NAME,
                TicketStatus::PENDING,
                [
                    TicketInterface::ACTIVATE_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME
                ],
                false
            ],

            [
                TicketInterface::DOWNLOAD_ACTION_NAME,
                TicketStatus::UNUSED,
                [
                    TicketInterface::CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                    TicketInterface::DOWNLOAD_ACTION_NAME,
                    TicketInterface::SEND_EMAIL_ACTION_NAME,
                ],
                true
            ],
            [
                TicketInterface::DOWNLOAD_ACTION_NAME,
                TicketStatus::USED,
                [
                    TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME,
                ],
                false
            ],
            [
                TicketInterface::DOWNLOAD_ACTION_NAME,
                TicketStatus::CANCELED,
                [],
                false
            ],
            [
                TicketInterface::DOWNLOAD_ACTION_NAME,
                TicketStatus::PENDING,
                [
                    TicketInterface::ACTIVATE_ACTION_NAME,
                    TicketInterface::CANCEL_ACTION_NAME
                ],
                false
            ],
        ];
    }
}
