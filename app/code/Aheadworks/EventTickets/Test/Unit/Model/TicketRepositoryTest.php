<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Api\Data\TicketSearchResultsInterface;
use Aheadworks\EventTickets\Model\Ticket;
use Aheadworks\EventTickets\Model\TicketRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Collection as TicketCollection;

/**
 * Class TicketRepositoryTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class TicketRepositoryTest extends TestCase
{
    /**
     * @var TicketRepository
     */
    private $model;

    /**
     * @var TicketResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TicketInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketInterfaceFactoryMock;

    /**
     * @var TicketCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketCollectionFactoryMock;

    /**
     * @var TicketSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var array
     */
    private $ticketData = [
        'id' => 1,
        'number' => 'number'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(TicketResourceModel::class, ['save', 'load', 'getIdByNumber']);
        $this->ticketInterfaceFactoryMock = $this->createPartialMock(TicketInterfaceFactory::class, ['create']);
        $this->ticketCollectionFactoryMock = $this->createPartialMock(TicketCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            TicketSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            TicketRepository::class,
            [
                'resource' => $this->resourceMock,
                'ticketInterfaceFactory' => $this->ticketInterfaceFactoryMock,
                'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createPartialMock(Ticket::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $ticketMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ticketData['id']);

        $this->assertSame($ticketMock, $this->model->save($ticketMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $exception = new \Exception('Exception message.');

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createPartialMock(Ticket::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(CouldNotSaveException::class);
        $this->model->save($ticketMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $ticketId = 1;
        $ticketNumber = 'number';

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('getIdByNumber')
            ->with($ticketNumber)
            ->willReturn($ticketId);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();

        $this->assertSame($ticketMock, $this->model->get($ticketNumber));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with ticket number = number
     */
    public function testGetOnException()
    {
        $ticketId = null;
        $ticketNumber = 'number';

        $this->resourceMock->expects($this->once())
            ->method('getIdByNumber')
            ->with($ticketNumber)
            ->willReturn($ticketId);

        $this->expectException(NoSuchEntityException::class);
        $this->model->get($ticketNumber);
    }

    /**
     * Testing of getById method
     */
    public function testGetById()
    {
        $ticketId = 1;

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();
        $ticketMock->expects($this->once())
            ->method('getId')
            ->willReturn($ticketId);

        $this->assertSame($ticketMock, $this->model->getById($ticketId));
    }

    /**
     * Testing of getById method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with ticketId = 1
     */
    public function testGetByIdOnException()
    {
        $ticketId = 1;

        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->createMock(Ticket::class);
        $this->ticketInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketMock, $ticketId)
            ->willReturnSelf();
        $ticketMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->model->getById($ticketId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var TicketCollection|\PHPUnit_Framework_MockObject_MockObject $ticketCollectionMock */
        $ticketCollectionMock = $this->createPartialMock(
            TicketCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TicketSearchResultsInterface::class);
        /** @var Ticket|\PHPUnit_Framework_MockObject_MockObject $ticketModelMock */
        $ticketModelMock = $this->createPartialMock(Ticket::class, ['getData']);
        /** @var TicketInterface|\PHPUnit_Framework_MockObject_MockObject $ticketMock */
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);

        $this->ticketCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($ticketCollectionMock, TicketInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $ticketCollectionMock);

        $ticketCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $ticketCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$ticketModelMock]);

        $this->ticketInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketMock);
        $ticketModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->ticketData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($ticketMock, $this->ticketData, TicketInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$ticketMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
