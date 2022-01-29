<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Api\Data\TicketTypeSearchResultsInterface;
use Aheadworks\EventTickets\Model\Ticket\Type as TicketType;
use Aheadworks\EventTickets\Model\TicketTypeRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketTypeSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type as TicketTypeResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\CollectionFactory as TicketTypeCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\Collection as TicketTypeCollection;

/**
 * Class TicketTypeRepositoryTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class TicketTypeRepositoryTest extends TestCase
{
    /**
     * @var TicketTypeRepository
     */
    private $model;

    /**
     * @var TicketTypeResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TicketTypeInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketTypeInterfaceFactoryMock;

    /**
     * @var TicketTypeCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketTypeCollectionFactoryMock;

    /**
     * @var TicketTypeSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var array
     */
    private $ticketTypeData = [
        'id' => 1,
        'name' => 'ticket type name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(TicketTypeResourceModel::class, ['save', 'load', 'setStoreId']);
        $this->ticketTypeInterfaceFactoryMock = $this->createPartialMock(TicketTypeInterfaceFactory::class, ['create']);
        $this->ticketTypeCollectionFactoryMock = $this->createPartialMock(
            TicketTypeCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            TicketTypeSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            TicketTypeRepository::class,
            [
                'resource' => $this->resourceMock,
                'ticketTypeInterfaceFactory' => $this->ticketTypeInterfaceFactoryMock,
                'ticketTypeCollectionFactory' => $this->ticketTypeCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'storeManager' => $this->storeManagerMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var TicketTypeInterface|\PHPUnit_Framework_MockObject_MockObject $ticketTypeMock */
        $ticketTypeMock = $this->createPartialMock(TicketType::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $ticketTypeMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ticketTypeData['id']);

        $this->assertSame($ticketTypeMock, $this->model->save($ticketTypeMock));
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

        /** @var TicketTypeInterface|\PHPUnit_Framework_MockObject_MockObject $ticketTypeMock */
        $ticketTypeMock = $this->createPartialMock(TicketType::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(CouldNotSaveException::class);
        $this->model->save($ticketTypeMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $storeId = 1;
        $ticketTypeId = 1;

        /** @var TicketTypeInterface|\PHPUnit_Framework_MockObject_MockObject $ticketTypeMock */
        $ticketTypeMock = $this->createMock(TicketType::class);
        $this->ticketTypeInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketTypeMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->resourceMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketTypeMock, $ticketTypeId)
            ->willReturnSelf();
        $ticketTypeMock->expects($this->once())
            ->method('getId')
            ->willReturn($ticketTypeId);

        $this->assertSame($ticketTypeMock, $this->model->get($ticketTypeId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with ticketTypeId = 1
     */
    public function testGetOnException()
    {
        $storeId = 1;
        $ticketTypeId = 1;

        /** @var TicketTypeInterface|\PHPUnit_Framework_MockObject_MockObject $ticketTypeMock */
        $ticketTypeMock = $this->createMock(TicketType::class);
        $this->ticketTypeInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketTypeMock);

        $this->resourceMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ticketTypeMock, $ticketTypeId)
            ->willReturnSelf();
        $ticketTypeMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->model->get($ticketTypeId, $storeId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $storeId = 1;
        $collectionSize = 1;
        /** @var TicketTypeCollection|\PHPUnit_Framework_MockObject_MockObject $ticketTypeCollectionMock */
        $ticketTypeCollectionMock = $this->createPartialMock(
            TicketTypeCollection::class,
            ['getSize', 'getItems', 'setStoreId']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TicketTypeSearchResultsInterface::class);
        /** @var TicketType|\PHPUnit_Framework_MockObject_MockObject $ticketTypeModelMock */
        $ticketTypeModelMock = $this->createPartialMock(TicketType::class, ['getData']);
        /** @var TicketTypeInterface|\PHPUnit_Framework_MockObject_MockObject $ticketTypeMock */
        $ticketTypeMock = $this->getMockForAbstractClass(TicketTypeInterface::class);

        $this->ticketTypeCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketTypeCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($ticketTypeCollectionMock, TicketTypeInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $ticketTypeCollectionMock);

        $ticketTypeCollectionMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $ticketTypeCollectionMock->expects($this->once())
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

        $ticketTypeCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$ticketTypeModelMock]);

        $this->ticketTypeInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ticketTypeMock);
        $ticketTypeModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->ticketTypeData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($ticketTypeMock, $this->ticketTypeData, TicketTypeInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$ticketTypeMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock, $storeId));
    }
}
