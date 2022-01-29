<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Email\EmailMetadataInterface;
use Aheadworks\EventTickets\Model\Ticket\Notifier;
use Magento\Framework\Exception\MailException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Email\Sender;
use Psr\Log\LoggerInterface;
use Aheadworks\EventTickets\Model\Ticket\Email\Processor as EmailProcessor;

/**
 * Class NotifierTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket
 */
class NotifierTest extends TestCase
{
    /**
     * @var Notifier
     */
    private $model;

    /**
     * @var Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderMock;

    /**
     * @var EmailProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailProcessorMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->senderMock = $this->createPartialMock(Sender::class, ['send']);
        $this->emailProcessorMock = $this->createPartialMock(EmailProcessor::class, ['process']);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->model = $objectManager->getObject(
            Notifier::class,
            [
                'sender' => $this->senderMock,
                'emailProcessor' => $this->emailProcessorMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test processActivatedTicketGroup method
     */
    public function testProcessActivatedTicketGroup()
    {
        $expected = true;
        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketGroup = [$ticketMock];

        $this->emailProcessorMock->expects($this->once())
            ->method('process')
            ->with($ticketGroup)
            ->willReturn($emailMetadataMock);
        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($emailMetadataMock);

        $this->assertEquals($expected, $this->model->processActivatedTicketGroup($ticketGroup));
    }

    /**
     * Test processActivatedTicketGroup method on exception
     */
    public function testProcessActivatedTicketGroupOnException()
    {
        $expected = false;
        $exception = new MailException(__('exception'));
        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketGroup = [$ticketMock];

        $this->emailProcessorMock->expects($this->once())
            ->method('process')
            ->with($ticketGroup)
            ->willReturn($emailMetadataMock);
        $this->senderMock->expects($this->once())
            ->method('send')
            ->willThrowException($exception);
        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->assertEquals($expected, $this->model->processActivatedTicketGroup($ticketGroup));
    }
}
