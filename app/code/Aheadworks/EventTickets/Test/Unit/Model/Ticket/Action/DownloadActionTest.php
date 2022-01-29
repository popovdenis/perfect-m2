<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Action\DownloadAction;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;

/**
 * Class DownloadActionTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action
 */
class DownloadActionTest extends TestCase
{
    /**
     * @var DownloadAction
     */
    private $object;

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
        $this->ticketStatusResolverMock = $this->createPartialMock(
            TicketStatusResolver::class,
            [
                'isActionAllowedForTicket'
            ]
        );

        $this->object = $objectManager->getObject(
            DownloadAction::class,
            [
                'ticketStatusResolver' => $this->ticketStatusResolverMock,
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $unavailableForActionTicket = $this->getTicketMockUnavailableForDownloadAction();
        $ticketAvailableForAction = $this->getTicketMockForDownloadAction();

        $this->ticketStatusResolverMock->expects($this->any())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::DOWNLOAD_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ],
                    [
                        TicketInterface::DOWNLOAD_ACTION_NAME,
                        $ticketAvailableForAction,
                        true
                    ]
                ]
            );

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
        $unavailableForActionTicket = $this->getTicketMockUnavailableForDownloadAction();
        $this->ticketStatusResolverMock->expects($this->never())
            ->method('isActionAllowedForTicket')
            ->willReturnMap(
                [
                    [
                        TicketInterface::DOWNLOAD_ACTION_NAME,
                        $unavailableForActionTicket,
                        false
                    ]
                ]
            )
        ;

        $this->assertTrue(is_array(
            $this->object->execute(
                []
            )
        ));
    }

    /**
     * Retrieve ticket mock for download action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockForDownloadAction()
    {
        $ticketMock = $this->getMockBuilder(TicketInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPdf'])
            ->getMockForAbstractClass();
        $ticketMock->expects($this->once())
            ->method('getPdf')
            ->willReturnSelf();

        return $ticketMock;
    }

    /**
     * Retrieve ticket mock unavailable for download action
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTicketMockUnavailableForDownloadAction()
    {
        $ticketMock = $this->getMockForAbstractClass(
            TicketInterface::class
        );

        return $ticketMock;
    }
}
