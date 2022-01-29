<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Pdf;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Email\AttachmentInterface;
use Aheadworks\EventTickets\Model\Email\AttachmentInterfaceFactory;
use Aheadworks\EventTickets\Model\Ticket\Pdf\Creator as TicketPdfCreator;

/**
 * Class PdfTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket
 */
class PdfTest extends TestCase
{
    /**
     * @var Pdf
     */
    private $model;

    /**
     * @var TicketPdfCreator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketPdfCreatorMock;

    /**
     * @var AttachmentInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attachmentInterfaceFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketPdfCreatorMock = $this->createPartialMock(TicketPdfCreator::class, ['create']);
        $this->attachmentInterfaceFactoryMock = $this->createPartialMock(AttachmentInterfaceFactory::class, ['create']);
        $this->model = $objectManager->getObject(
            Pdf::class,
            [
                'ticketPdfCreator' => $this->ticketPdfCreatorMock,
                'attachmentInterfaceFactory' => $this->attachmentInterfaceFactoryMock
            ]
        );
    }

    /**
     * Test getTicketPdf method
     */
    public function testGetTicketPdf()
    {
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $attachmentMock = $this->getMockForAbstractClass(AttachmentInterface::class);
        $attachment = 'attachment';
        $ticketNumber = 'number';
        $fileName = $ticketNumber . '.pdf';
        $expected = $attachmentMock;

        $this->attachmentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attachmentMock);

        $this->ticketPdfCreatorMock->expects($this->once())
            ->method('create')
            ->with($ticketMock)
            ->willReturn($attachment);
        $attachmentMock->expects($this->once())
            ->method('setAttachment')
            ->with($attachment)
            ->willReturnSelf();
        $ticketMock->expects($this->once())
            ->method('getNumber')
            ->willReturn($ticketNumber);
        $attachmentMock->expects($this->once())
            ->method('setFileName')
            ->with($fileName)
            ->willReturnSelf();

        $this->assertEquals($expected, $this->model->getTicketPdf($ticketMock));
    }
}
