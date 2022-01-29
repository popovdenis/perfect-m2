<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Email;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Email\Processor;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Email\AttachmentInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Email\EmailMetadataInterface;
use Aheadworks\EventTickets\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\EventTickets\Model\Source\Ticket\EmailVariables;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\Composite as VariableProcessorComposite;

/**
 * Class ProcessorTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Email
 */
class ProcessorTest extends TestCase
{
    /**
     * @var Processor
     */
    private $object;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var EmailMetadataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailMetadataFactoryMock;

    /**
     * @var VariableProcessorComposite|\PHPUnit_Framework_MockObject_MockObject
     */
    private $variableProcessorCompositeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->createPartialMock(
            Config::class,
            [
                'getNewTicketEmailTemplate',
                'getSenderName',
                'getSenderEmail',
            ]
        );
        $this->storeManagerMock = $this->getMockForAbstractClass(
            StoreManagerInterface::class,
            []
        );
        $this->emailMetadataFactoryMock = $this->createPartialMock(
            EmailMetadataInterfaceFactory::class,
            [
                'create'
            ]
        );
        $this->variableProcessorCompositeMock = $this->createPartialMock(
            VariableProcessorComposite::class,
            [
                'prepareVariables'
            ]
        );

        $this->object = $objectManager->getObject(
            Processor::class,
            [
                'config' => $this->configMock,
                'storeManager' => $this->storeManagerMock,
                'emailMetadataFactory' => $this->emailMetadataFactoryMock,
                'variableProcessorComposite' => $this->variableProcessorCompositeMock,
            ]
        );
    }

    /**
     * Testing of process method
     */
    public function testProcessSingleTicket()
    {
        $ticketToProcess = $this->getMockBuilder(TicketInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getPdf',
                    'getAttendeeName',
                    'getAttendeeEmail'
                ]
            )->getMockForAbstractClass();
        $attachment = $this->getMockForAbstractClass(
            AttachmentInterface::class
        );
        $storeId = 1;
        $emailMetadata = $this->getMockForAbstractClass(
            EmailMetadataInterface::class
        );
        $emailTemplate = 'email_template';
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];

        $ticketToProcess->expects($this->once())
            ->method('getPdf')
            ->willReturn($attachment);
        $ticketToProcess->expects($this->exactly(2))
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->configMock->expects($this->once())
            ->method('getNewTicketEmailTemplate')
            ->with($storeId)
            ->willReturn($emailTemplate);
        $emailMetadata->expects($this->once())
            ->method('setTemplateId')
            ->with($emailTemplate)
            ->willReturnSelf();

        $emailMetadata->expects($this->once())
            ->method('setTemplateOptions')
            ->with($templateOptions)
            ->willReturnSelf();

        $storeMock = $this->getMockForAbstractClass(
            StoreInterface::class
        );
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);
        $templateVariables = [
            EmailVariables::TICKET => $ticketToProcess,
            EmailVariables::STORE => $storeMock,
            EmailVariables::TICKETS => [$ticketToProcess]
        ];
        $preparedTemplateVariables = [
            EmailVariables::TICKET => $ticketToProcess,
            EmailVariables::STORE => $storeMock,
            EmailVariables::EVENT_START_DATE_FORMATTED => '2018-12-12 00:00',
            EmailVariables::EVENT_END_DATE_FORMATTED => '2018-12-24 00:00',
            EmailVariables::EVENT_IMAGE_URL => 'event_image_url',
        ];
        $this->variableProcessorCompositeMock->expects($this->once())
            ->method('prepareVariables')
            ->with($templateVariables)
            ->willReturn($preparedTemplateVariables);

        $emailMetadata->expects($this->once())
            ->method('setTemplateVariables')
            ->with($preparedTemplateVariables)
            ->willReturnSelf();

        $senderName = 'sender_name';
        $this->configMock->expects($this->once())
            ->method('getSenderName')
            ->with($storeId)
            ->willReturn($senderName);
        $emailMetadata->expects($this->once())
            ->method('setSenderName')
            ->with($senderName)
            ->willReturnSelf();

        $senderEmail = 'sender_email';
        $this->configMock->expects($this->once())
            ->method('getSenderEmail')
            ->with($storeId)
            ->willReturn($senderEmail);
        $emailMetadata->expects($this->once())
            ->method('setSenderEmail')
            ->with($senderEmail)
            ->willReturnSelf();

        $recipientName = 'recipient_name';
        $ticketToProcess->expects($this->once())
            ->method('getAttendeeName')
            ->willReturn($recipientName);
        $emailMetadata->expects($this->once())
            ->method('setRecipientName')
            ->with($recipientName)
            ->willReturnSelf();

        $recipientEmail = 'recipient_email';
        $ticketToProcess->expects($this->once())
            ->method('getAttendeeEmail')
            ->willReturn($recipientEmail);
        $emailMetadata->expects($this->once())
            ->method('setRecipientEmail')
            ->with($recipientEmail)
            ->willReturnSelf();

        $emailMetadata->expects($this->once())
            ->method('setAttachments')
            ->with([$attachment])
            ->willReturnSelf();

        $this->emailMetadataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($emailMetadata);

        $this->assertEquals($emailMetadata, $this->object->process([$ticketToProcess]));
    }

    /**
     * Testing of process method
     */
    public function testProcessTicketsArray()
    {
        $ticketToProcess = $this->getMockBuilder(TicketInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getPdf',
                    'getAttendeeName',
                    'getAttendeeEmail'
                ]
            )->getMockForAbstractClass();

        $attachmentTicket = $this->getMockBuilder(TicketInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getPdf',
                    'getAttendeeName',
                    'getAttendeeEmail'
                ]
            )->getMockForAbstractClass();

        $attachment = $this->getMockForAbstractClass(
            AttachmentInterface::class
        );
        $storeId = 1;
        $emailMetadata = $this->getMockForAbstractClass(
            EmailMetadataInterface::class
        );
        $emailTemplate = 'email_template';
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];

        $ticketToProcess->expects($this->once())
            ->method('getPdf')
            ->willReturn($attachment);
        $attachmentTicket->expects($this->once())
            ->method('getPdf')
            ->willReturn($attachment);
        $ticketToProcess->expects($this->exactly(2))
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->configMock->expects($this->once())
            ->method('getNewTicketEmailTemplate')
            ->with($storeId)
            ->willReturn($emailTemplate);
        $emailMetadata->expects($this->once())
            ->method('setTemplateId')
            ->with($emailTemplate)
            ->willReturnSelf();

        $emailMetadata->expects($this->once())
            ->method('setTemplateOptions')
            ->with($templateOptions)
            ->willReturnSelf();

        $storeMock = $this->getMockForAbstractClass(
            StoreInterface::class
        );
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);
        $templateVariables = [
            EmailVariables::TICKET => $ticketToProcess,
            EmailVariables::STORE => $storeMock,
            EmailVariables::TICKETS => [
                $ticketToProcess,
                $ticketToProcess
            ]
        ];
        $preparedTemplateVariables = [
            EmailVariables::TICKET => $ticketToProcess,
            EmailVariables::STORE => $storeMock,
            EmailVariables::EVENT_START_DATE_FORMATTED => '2018-12-12 00:00',
            EmailVariables::EVENT_END_DATE_FORMATTED => '2018-12-24 00:00',
            EmailVariables::EVENT_IMAGE_URL => 'event_image_url',
        ];
        $this->variableProcessorCompositeMock->expects($this->once())
            ->method('prepareVariables')
            ->with($templateVariables)
            ->willReturn($preparedTemplateVariables);

        $emailMetadata->expects($this->once())
            ->method('setTemplateVariables')
            ->with($preparedTemplateVariables)
            ->willReturnSelf();

        $senderName = 'sender_name';
        $this->configMock->expects($this->once())
            ->method('getSenderName')
            ->with($storeId)
            ->willReturn($senderName);
        $emailMetadata->expects($this->once())
            ->method('setSenderName')
            ->with($senderName)
            ->willReturnSelf();

        $senderEmail = 'sender_email';
        $this->configMock->expects($this->once())
            ->method('getSenderEmail')
            ->with($storeId)
            ->willReturn($senderEmail);
        $emailMetadata->expects($this->once())
            ->method('setSenderEmail')
            ->with($senderEmail)
            ->willReturnSelf();

        $recipientName = 'recipient_name';
        $ticketToProcess->expects($this->once())
            ->method('getAttendeeName')
            ->willReturn($recipientName);
        $emailMetadata->expects($this->once())
            ->method('setRecipientName')
            ->with($recipientName)
            ->willReturnSelf();

        $recipientEmail = 'recipient_email';
        $ticketToProcess->expects($this->once())
            ->method('getAttendeeEmail')
            ->willReturn($recipientEmail);
        $emailMetadata->expects($this->once())
            ->method('setRecipientEmail')
            ->with($recipientEmail)
            ->willReturnSelf();

        $emailMetadata->expects($this->once())
            ->method('setAttachments')
            ->with([$attachment, $attachment])
            ->willReturnSelf();

        $this->emailMetadataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($emailMetadata);

        $this->assertEquals($emailMetadata, $this->object->process([$ticketToProcess, $attachmentTicket]));
    }
}
