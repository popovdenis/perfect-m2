<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Action\TicketResolver;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class TicketResolverTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action
 */
class TicketResolverTest extends TestCase
{
    /**
     * @var TicketResolver
     */
    private $object;

    /**
     * @var TicketRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketRepositoryMock;

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

        $this->object = $objectManager->getObject(
            TicketResolver::class,
            [
                'ticketRepository' => $this->ticketRepositoryMock,
            ]
        );
    }

    /**
     * Testing of getResolvedTicketsArray method
     */
    public function testGetResolvedTicketsEmptyArray()
    {
        $ticketsDataArray = [];

        $this->assertEquals([], $this->object->getResolvedTicketsArray($ticketsDataArray));
    }

    /**
     * Testing of getResolvedTicketsArray method
     */
    public function testGetResolvedTicketsArrayById()
    {
        $ticketIdsArray = [1, 2, 3];
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketRepositoryMock->expects($this->exactly(count($ticketIdsArray)))
            ->method('get')
            ->willThrowException(new NoSuchEntityException());

        $this->ticketRepositoryMock->expects($this->exactly(count($ticketIdsArray)))
            ->method('getById')
            ->willReturn($ticketMock);

        $this->assertTrue(is_array($this->object->getResolvedTicketsArray($ticketIdsArray)));
    }

    /**
     * Testing of getResolvedTicketsArray method
     */
    public function testGetResolvedTicketsArrayByNumber()
    {
        $ticketNumbersArray = ['ABC123', 'DEF466'];
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketRepositoryMock->expects($this->exactly(count($ticketNumbersArray)))
            ->method('get')
            ->willReturn($ticketMock);

        $this->ticketRepositoryMock->expects($this->never())
            ->method('getById');

        $this->assertTrue(is_array($this->object->getResolvedTicketsArray($ticketNumbersArray)));
    }

    /**
     * Testing of getResolvedTicket method
     */
    public function testGetResolvedTicketEmptyData()
    {
        $ticketData = null;
        $expected = null;

        $this->ticketRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ticketData)
            ->willThrowException(new NoSuchEntityException());

        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketData)
            ->willThrowException(new \Exception());

        $this->assertEquals($expected, $this->object->getResolvedTicket($ticketData));
    }

    /**
     * Testing of getResolvedTicket method
     */
    public function testGetResolvedTicketById()
    {
        $ticketId = 1;
        $expected = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ticketId)
            ->willThrowException(new NoSuchEntityException());

        $this->ticketRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($ticketId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getResolvedTicket($ticketId));
    }

    /**
     * Testing of getResolvedTicket method
     */
    public function testGetResolvedTicketByNumber()
    {
        $ticketNumber = 'ABC123';
        $expected = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ticketNumber)
            ->willReturn($expected);

        $this->ticketRepositoryMock->expects($this->never())
            ->method('getById')
            ->with($ticketNumber);

        $this->assertEquals($expected, $this->object->getResolvedTicket($ticketNumber));
    }

    /**
     * Testing of getResolvedTicket method
     */
    public function testGetResolvedTicket()
    {
        $ticket = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketRepositoryMock->expects($this->never())
            ->method('get');

        $this->ticketRepositoryMock->expects($this->never())
            ->method('getById');

        $this->assertEquals($ticket, $this->object->getResolvedTicket($ticket));
    }
}
