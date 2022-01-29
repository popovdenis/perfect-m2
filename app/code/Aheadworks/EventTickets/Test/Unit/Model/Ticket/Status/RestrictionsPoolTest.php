<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Status;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Ticket\Status\RestrictionsPool;
use Aheadworks\EventTickets\Model\Ticket\Status\RestrictionsInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\RestrictionsInterfaceFactory;

/**
 * Class RestrictionsPoolTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Status
 */
class RestrictionsPoolTest extends TestCase
{
    /**
     * @var RestrictionsPool
     */
    private $object;

    /**
     * @var RestrictionsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $restrictionsFactoryMock;

    /**
     * @var array
     */
    private $restrictionsData;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->restrictionsFactoryMock = $this->createPartialMock(
            RestrictionsInterfaceFactory::class,
            [
                'create'
            ]
        );

        $this->restrictionsData = [
            \Aheadworks\EventTickets\Model\Source\Ticket\Status::UNUSED => [
                'allowed_actions_names' => [
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::CHECK_IN_ACTION_NAME,
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::CANCEL_ACTION_NAME,
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::DOWNLOAD_ACTION_NAME,
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::SEND_EMAIL_ACTION_NAME,
                ]
            ],
            \Aheadworks\EventTickets\Model\Source\Ticket\Status::USED => [
                'allowed_actions_names' => [
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::UNDO_CHECK_IN_ACTION_NAME,
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::CANCEL_ACTION_NAME,
                ]
            ],
            \Aheadworks\EventTickets\Model\Source\Ticket\Status::CANCELED => [
                'allowed_actions_names' => []
            ],
            \Aheadworks\EventTickets\Model\Source\Ticket\Status::PENDING => [
                'allowed_actions_names' => [
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::ACTIVATE_ACTION_NAME,
                    \Aheadworks\EventTickets\Api\Data\TicketInterface::CANCEL_ACTION_NAME,
                ]
            ],
        ];

        $this->object = $objectManager->getObject(
            RestrictionsPool::class,
            [
                'restrictionsFactory' => $this->restrictionsFactoryMock,
                'restrictionsData' => $this->restrictionsData
            ]
        );
    }

    /**
     * Testing of getRestrictions method with incorrect status
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown status: 10 requested
     */
    public function testGetRestrictionsIncorrectStatus()
    {
        $status = 10;

        $this->restrictionsFactoryMock->expects($this->never())
            ->method('create');

        $this->expectException(\Exception::class);
        $this->object->getRestrictions($status);
    }

    /**
     * Testing of getRestrictions method with incorrect instance
     * @expectedException \Exception
     * @expectedExceptionMessage Restrictions instance does not implement required interface.
     */
    public function testGetRestrictionsIncorrectInstance()
    {
        $status = \Aheadworks\EventTickets\Model\Source\Ticket\Status::UNUSED;
        $restrictionInstance = $this->createPartialMock(
            \Magento\Framework\Model\AbstractModel::class,
            []
        );
        $restrictionsData = $this->restrictionsData[$status];

        $this->restrictionsFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $restrictionsData])
            ->willReturn($restrictionInstance)
        ;

        $this->expectException(\Exception::class);
        $this->object->getRestrictions($status);
    }

    /**
     * Testing of getRestrictions method
     */
    public function testGetRestrictions()
    {
        $status = \Aheadworks\EventTickets\Model\Source\Ticket\Status::UNUSED;
        $restrictionInstance = $this->getMockForAbstractClass(
            RestrictionsInterface::class
        );
        $restrictionsData = $this->restrictionsData[$status];

        $this->restrictionsFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $restrictionsData])
            ->willReturn($restrictionInstance)
        ;

        $this->assertTrue($this->object->getRestrictions($status) instanceof RestrictionsInterface);
    }
}
