<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Api\Data\SectorSearchResultsInterface;
use Aheadworks\EventTickets\Model\Sector;
use Aheadworks\EventTickets\Model\SectorRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SectorSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Sector as SectorResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Sector\CollectionFactory as SectorCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Sector\Collection as SectorCollection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SectorRepositoryTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class SectorRepositoryTest extends TestCase
{
    /**
     * @var SectorRepository
     */
    private $model;

    /**
     * @var SectorResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var SectorInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sectorInterfaceFactoryMock;

    /**
     * @var SectorCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sectorCollectionFactoryMock;

    /**
     * @var SectorSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $sectorData = [
        'id' => 1,
        'name' => 'sector name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(SectorResourceModel::class, ['save', 'load', 'setStoreId']);
        $this->sectorInterfaceFactoryMock = $this->createPartialMock(SectorInterfaceFactory::class, ['create']);
        $this->sectorCollectionFactoryMock = $this->createPartialMock(SectorCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            SectorSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            SectorRepository::class,
            [
                'resource' => $this->resourceMock,
                'sectorInterfaceFactory' => $this->sectorInterfaceFactoryMock,
                'sectorCollectionFactory' => $this->sectorCollectionFactoryMock,
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
        /** @var SectorInterface|\PHPUnit_Framework_MockObject_MockObject $sectorMock */
        $sectorMock = $this->createPartialMock(Sector::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $sectorMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->sectorData['id']);

        $this->assertSame($sectorMock, $this->model->save($sectorMock));
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

        /** @var SectorInterface|\PHPUnit_Framework_MockObject_MockObject $sectorMock */
        $sectorMock = $this->createPartialMock(Sector::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(CouldNotSaveException::class);
        $this->model->save($sectorMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $storeId = 1;
        $sectorId = 1;

        /** @var SectorInterface|\PHPUnit_Framework_MockObject_MockObject $sectorMock */
        $sectorMock = $this->createMock(Sector::class);
        $this->sectorInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sectorMock);

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
            ->with($sectorMock, $sectorId)
            ->willReturnSelf();
        $sectorMock->expects($this->once())
            ->method('getId')
            ->willReturn($sectorId);

        $this->assertSame($sectorMock, $this->model->get($sectorId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with sectorId = 1
     */
    public function testGetOnException()
    {
        $storeId = 1;
        $sectorId = 1;

        /** @var SectorInterface|\PHPUnit_Framework_MockObject_MockObject $sectorMock */
        $sectorMock = $this->createMock(Sector::class);
        $this->sectorInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sectorMock);

        $this->resourceMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($sectorMock, $sectorId)
            ->willReturnSelf();
        $sectorMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->model->get($sectorId, $storeId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $storeId = 1;
        $collectionSize = 1;
        /** @var SectorCollection|\PHPUnit_Framework_MockObject_MockObject $sectorCollectionMock */
        $sectorCollectionMock = $this->createPartialMock(
            SectorCollection::class,
            ['getSize', 'getItems', 'setStoreId']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(SectorSearchResultsInterface::class);
        /** @var Sector|\PHPUnit_Framework_MockObject_MockObject $sectorModelMock */
        $sectorModelMock = $this->createPartialMock(Sector::class, ['getData']);
        /** @var SectorInterface|\PHPUnit_Framework_MockObject_MockObject $sectorMock */
        $sectorMock = $this->getMockForAbstractClass(SectorInterface::class);

        $this->sectorCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sectorCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($sectorCollectionMock, SectorInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $sectorCollectionMock);

        $sectorCollectionMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $sectorCollectionMock->expects($this->once())
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

        $sectorCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$sectorModelMock]);

        $this->sectorInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sectorMock);
        $sectorModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->sectorData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($sectorMock, $this->sectorData, SectorInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$sectorMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock, $storeId));
    }
}
