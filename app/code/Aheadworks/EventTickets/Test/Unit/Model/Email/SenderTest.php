<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Email;

use Aheadworks\EventTickets\Model\Email\AttachmentInterface;
use Aheadworks\EventTickets\Model\Email\EmailMetadataInterface;
use Aheadworks\EventTickets\Model\Email\Sender;
use Aheadworks\EventTickets\Model\Email\Template\TransportBuilderInterface;
use Aheadworks\EventTickets\Model\Email\Template\TransportBuilder\Factory as TransportBuilderFactory;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class SenderTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Email
 */
class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $object;

    /**
     * @var TransportBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportBuilderMock;

    /**
     * @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->transportBuilderMock = $this->createMock(TransportBuilderInterface::class);
        $this->transportMock = $this->createMock(TransportInterface::class);
        $transportBuilderFactoryMock = $this->createMock(TransportBuilderFactory::class);
        $transportBuilderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->transportBuilderMock);

        $this->object = $objectManager->getObject(
            Sender::class,
            ['transportBuilderFactory' => $transportBuilderFactoryMock]
        );
    }

    /**
     * Testing of send method
     */
    public function testSend()
    {
        $expectedValue = true;
        $emailMetadataMock = $this->initSender();
        $this->transportMock->expects($this->once())
            ->method('sendMessage');

        $this->assertEquals($expectedValue, $this->object->send($emailMetadataMock));
    }

    /**
     * Testing of send method on exception
     *
     * @expectedException \Magento\Framework\Exception\MailException
     */
    public function testSendOnException()
    {
        $exception = new MailException(__('Exception message.'));
        $emailMetadataMock = $this->initSender();

        $this->transportMock->expects($this->once())
            ->method('sendMessage')
            ->willThrowException($exception);

        $this->expectException(MailException::class);
        $this->object->send($emailMetadataMock);
    }

    /**
     * Init sender method
     *
     * @return EmailMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function initSender()
    {
        $attachmentMock = $this->getMockForAbstractClass(AttachmentInterface::class);
        $attachment = [AttachmentInterface::ATTACHMENT => 'attachment', AttachmentInterface::FILE_NAME => 'file.pdf'];
        $emailMetadata = [
            EmailMetadataInterface::TEMPLATE_ID => 'template_id',
            EmailMetadataInterface::TEMPLATE_OPTIONS => ['opt1', ['opt2']],
            EmailMetadataInterface::TEMPLATE_VARIABLES => ['var1', 'var2'],
            EmailMetadataInterface::SENDER_NAME => 'sender_name',
            EmailMetadataInterface::SENDER_EMAIL => 'sender_email',
            EmailMetadataInterface::RECIPIENT_NAME => 'recipient_name',
            EmailMetadataInterface::RECIPIENT_EMAIL => 'recipient_email',
            EmailMetadataInterface::ATTACHMENTS => [$attachmentMock],
        ];

        $attachmentMock->expects($this->once())
            ->method('getAttachment')
            ->willReturn($attachment[AttachmentInterface::ATTACHMENT]);
        $attachmentMock->expects($this->once())
            ->method('getFileName')
            ->willReturn($attachment[AttachmentInterface::FILE_NAME]);

        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateId')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_ID]);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateOptions')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_OPTIONS]);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateVariables')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_VARIABLES]);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderName')
            ->willReturn($emailMetadata[EmailMetadataInterface::SENDER_NAME]);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderEmail')
            ->willReturn($emailMetadata[EmailMetadataInterface::SENDER_EMAIL]);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($emailMetadata[EmailMetadataInterface::RECIPIENT_NAME]);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($emailMetadata[EmailMetadataInterface::RECIPIENT_EMAIL]);
        $emailMetadataMock->expects($this->once())
            ->method('getAttachments')
            ->willReturn($emailMetadata[EmailMetadataInterface::ATTACHMENTS]);

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_ID])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_OPTIONS])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_VARIABLES])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setFrom')
            ->with([
                'name' => $emailMetadata[EmailMetadataInterface::SENDER_NAME],
                'email' => $emailMetadata[EmailMetadataInterface::SENDER_EMAIL]
            ])->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->with(
                $emailMetadata[EmailMetadataInterface::RECIPIENT_EMAIL],
                $emailMetadata[EmailMetadataInterface::RECIPIENT_NAME]
            )->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('addAttachment')
            ->with($attachment[AttachmentInterface::ATTACHMENT], $attachment[AttachmentInterface::FILE_NAME])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->willReturn($this->transportMock);

        return $emailMetadataMock;
    }
}
