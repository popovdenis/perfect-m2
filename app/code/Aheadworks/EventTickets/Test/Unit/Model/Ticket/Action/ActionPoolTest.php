<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\ActionPool;
use Aheadworks\EventTickets\Model\Ticket\Action\Metadata\ActionMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\Metadata\ActionMetadataPool;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\EventTickets\Model\Ticket\Action\AbstractAction;

/**
 * Class ActionPoolTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Action
 */
class ActionPoolTest extends TestCase
{
    /**
     * @var ActionPool
     */
    private $object;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var ActionMetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $actionMetadataPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->objectManagerMock = $this->getMockForAbstractClass(
            ObjectManagerInterface::class,
            [],
            '',
            false,
            true,
            true,
            [
                'create'
            ]
        );
        $this->actionMetadataPoolMock = $this->createPartialMock(
            ActionMetadataPool::class,
            [
                'getMetadata'
            ]
        );

        $this->object = $objectManager->getObject(
            ActionPool::class,
            [
                'objectManager' => $this->objectManagerMock,
                'actionMetadataPool' => $this->actionMetadataPoolMock,
            ]
        );
    }

    /**
     * Testing of getAction method with incorrect action code
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown ticket action metadata: incorrect_action_code requested
     */
    public function testGetActionIncorrectActionCode()
    {
        $actionCode = 'incorrect_action_code';

        $this->actionMetadataPoolMock->expects($this->once())
            ->method('getMetadata')
            ->with($actionCode)
            ->willThrowException(
                new \Exception(sprintf('Unknown ticket action metadata: %s requested', $actionCode))
            );

        $this->expectException(\Exception::class);
        $this->object->getAction($actionCode);
    }

    /**
     * Testing of getAction method with incorrect action instance
     * @expectedException \Exception
     * @expectedExceptionMessage Ticket action checkIn does not implement required interface.
     */
    public function testGetActionIncorrectActionInstance()
    {
        $actionCode = TicketInterface::CHECK_IN_ACTION_NAME;
        $actionClassName = AbstractModel::class;
        $actionMock = $this->createPartialMock(
            $actionClassName,
            []
        );

        $actionMetadata = $this->getMockForAbstractClass(
            ActionMetadataInterface::class,
            [],
            '',
            false,
            true,
            true,
            [
                'getClassName'
            ]
        );
        $actionMetadata->expects($this->once())
            ->method('getClassName')
            ->willReturn($actionClassName);

        $this->actionMetadataPoolMock->expects($this->once())
            ->method('getMetadata')
            ->with($actionCode)
            ->willReturn($actionMetadata);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($actionClassName)
            ->willReturn($actionMock);

        $this->expectException(\Exception::class);
        $this->object->getAction($actionCode);
    }

    /**
     * Testing of getAction method
     */
    public function testGetAction()
    {
        $actionCode = TicketInterface::CHECK_IN_ACTION_NAME;
        $actionClassName = AbstractAction::class;
        $actionMock = $this->getMockForAbstractClass(
            $actionClassName,
            [],
            '',
            false
        );

        $actionMetadata = $this->getMockForAbstractClass(
            ActionMetadataInterface::class,
            [],
            '',
            false,
            true,
            true,
            [
                'getClassName'
            ]
        );
        $actionMetadata->expects($this->once())
            ->method('getClassName')
            ->willReturn($actionClassName);

        $this->actionMetadataPoolMock->expects($this->once())
            ->method('getMetadata')
            ->with($actionCode)
            ->willReturn($actionMetadata);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($actionClassName)
            ->willReturn($actionMock);

        $this->assertTrue(($this->object->getAction($actionCode) instanceof AbstractAction));
    }
}
