<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action\Metadata;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\CheckInAction;
use Aheadworks\EventTickets\Model\Ticket\Action\Metadata\ActionMetadataPool;
use Aheadworks\EventTickets\Model\Ticket\Action\Metadata\ActionMetadataInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\Metadata\ActionMetadataInterfaceFactory;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ActionMetadataPoolTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action\Metadata
 */
class ActionMetadataPoolTest extends TestCase
{
    /**
     * @var ActionMetadataPool
     */
    private $object;

    /**
     * @var ActionMetadataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataFactoryMock;

    /**
     * @var array
     */
    private $metadata;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->metadataFactoryMock = $this->createPartialMock(
            ActionMetadataInterfaceFactory::class,
            [
                'create'
            ]
        );

        $this->metadata = [
            'checkIn' => [
                'name' => TicketInterface::CHECK_IN_ACTION_NAME,
                'class_name' => CheckInAction::class,
            ],
        ];

        $this->object = $objectManager->getObject(
            ActionMetadataPool::class,
            [
                'metadataFactory' => $this->metadataFactoryMock,
                'metadata' => $this->metadata,
            ]
        );
    }

    /**
     * Testing of getMetadata method with incorrect action name
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown ticket action metadata: incorrect_action_name requested
     */
    public function testGetMetadataIncorrectActionName()
    {
        $actionName = 'incorrect_action_name';

        $this->expectException(\Exception::class);
        $this->object->getMetadata($actionName);
    }

    /**
     * Testing of getMetadata method with incorrect metadata instance
     * @expectedException \Exception
     * @expectedExceptionMessage Metadata instance does not implement required interface.
     */
    public function testGetMetadataIncorrectMetadataInstance()
    {
        $actionName = TicketInterface::CHECK_IN_ACTION_NAME;
        $actionMetadata = $this->metadata[$actionName];
        $metadataInstance = $this->getMockForAbstractClass(
            AbstractModel::class,
            [],
            '',
            false
        );

        $this->metadataFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $actionMetadata])
            ->willReturn($metadataInstance);

        $this->expectException(\Exception::class);
        $this->object->getMetadata($actionName);
    }

    /**
     * Testing of getMetadata method
     */
    public function testGetMetadata()
    {
        $actionName = TicketInterface::CHECK_IN_ACTION_NAME;
        $actionMetadata = $this->metadata[$actionName];
        $metadataInstance = $this->getMockForAbstractClass(
            ActionMetadataInterface::class,
            [],
            '',
            false
        );

        $this->metadataFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $actionMetadata])
            ->willReturn($metadataInstance);

        $this->assertTrue(($this->object->getMetadata($actionName) instanceof ActionMetadataInterface));
    }
}
