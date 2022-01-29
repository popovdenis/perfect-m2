<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Api\Data\SpaceSearchResultsInterface;
use Aheadworks\EventTickets\Model\Space;
use Aheadworks\EventTickets\Model\SpaceRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SpaceSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Space as SpaceResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Space\CollectionFactory as SpaceCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Space\Collection as SpaceCollection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SpaceRepositoryTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class SpaceRepositoryTest extends TestCase
{
    /**
     * @var SpaceRepository
     */
    private $model;

    /**
     * @var SpaceResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var SpaceInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $spaceInterfaceFactoryMock;

    /**
     * @var SpaceCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $spaceCollectionFactoryMock;

    /**
     * @var SpaceSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $spaceData = [
        'id' => 1,
        'name' => 'space name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(SpaceResourceModel::class, ['save', 'load', 'setStoreId']);
        $this->spaceInterfaceFactoryMock = $this->createPartialMock(SpaceInterfaceFactory::class, ['create']);
        $this->spaceCollectionFactoryMock = $this->createPartialMock(SpaceCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            SpaceSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            SpaceRepository::class,
            [
                'resource' => $this->resourceMock,
                'spaceInterfaceFactory' => $this->spaceInterfaceFactoryMock,
                'spaceCollectionFactory' => $this->spaceCollectionFactoryMock,
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
        /** @var SpaceInterface|\PHPUnit_Framework_MockObject_MockObject $spaceMock */
        $spaceMock = $this->createPartialMock(Space::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $spaceMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->spaceData['id']);

        $this->assertSame($spaceMock, $this->model->save($spaceMock));
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

        /** @var SpaceInterface|\PHPUnit_Framework_MockObject_MockObject $spaceMock */
        $spaceMock = $this->createPartialMock(Space::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(CouldNotSaveException::class);
        $this->model->save($spaceMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $storeId = 1;
        $spaceId = 1;

        /** @var SpaceInterface|\PHPUnit_Framework_MockObject_MockObject $spaceMock */
        $spaceMock = $this->createMock(Space::class);
        $this->spaceInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($spaceMock);

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
            ->with($spaceMock, $spaceId)
            ->willReturnSelf();
        $spaceMock->expects($this->once())
            ->method('getId')
            ->willReturn($spaceId);

        $this->assertSame($spaceMock, $this->model->get($spaceId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with spaceId = 1
     */
    public function testGetOnException()
    {
        $storeId = 1;
        $spaceId = 1;

        /** @var SpaceInterface|\PHPUnit_Framework_MockObject_MockObject $spaceMock */
        $spaceMock = $this->createMock(Space::class);
        $this->spaceInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($spaceMock);

        $this->resourceMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($spaceMock, $spaceId)
            ->willReturnSelf();
        $spaceMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->model->get($spaceId, $storeId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $storeId = 1;
        $collectionSize = 1;
        /** @var SpaceCollection|\PHPUnit_Framework_MockObject_MockObject $spaceCollectionMock */
        $spaceCollectionMock = $this->createPartialMock(
            SpaceCollection::class,
            ['getSize', 'getItems', 'setStoreId']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(SpaceSearchResultsInterface::class);
        /** @var Space|\PHPUnit_Framework_MockObject_MockObject $spaceModelMock */
        $spaceModelMock = $this->createPartialMock(Space::class, ['getData']);
        /** @var SpaceInterface|\PHPUnit_Framework_MockObject_MockObject $spaceMock */
        $spaceMock = $this->getMockForAbstractClass(SpaceInterface::class);

        $this->spaceCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($spaceCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($spaceCollectionMock, SpaceInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $spaceCollectionMock);

        $spaceCollectionMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $spaceCollectionMock->expects($this->once())
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

        $spaceCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$spaceModelMock]);

        $this->spaceInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($spaceMock);
        $spaceModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->spaceData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($spaceMock, $this->spaceData, SpaceInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$spaceMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock, $storeId));
    }
}
