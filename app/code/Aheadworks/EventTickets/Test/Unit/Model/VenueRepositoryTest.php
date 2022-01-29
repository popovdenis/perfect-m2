<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Api\Data\VenueSearchResultsInterface;
use Aheadworks\EventTickets\Model\Venue;
use Aheadworks\EventTickets\Model\VenueRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\VenueSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Venue as VenueResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Venue\CollectionFactory as VenueCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Venue\Collection as VenueCollection;

/**
 * Class VenueRepositoryTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class VenueRepositoryTest extends TestCase
{
    /**
     * @var VenueRepository
     */
    private $model;

    /**
     * @var VenueResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var VenueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $venueInterfaceFactoryMock;

    /**
     * @var VenueCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $venueCollectionFactoryMock;

    /**
     * @var VenueSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $venueData = [
        'id' => 1,
        'name' => 'venue name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(VenueResourceModel::class, ['save', 'load', 'setStoreId']);
        $this->venueInterfaceFactoryMock = $this->createPartialMock(VenueInterfaceFactory::class, ['create']);
        $this->venueCollectionFactoryMock = $this->createPartialMock(VenueCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            VenueSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            VenueRepository::class,
            [
                'resource' => $this->resourceMock,
                'venueInterfaceFactory' => $this->venueInterfaceFactoryMock,
                'venueCollectionFactory' => $this->venueCollectionFactoryMock,
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
        /** @var VenueInterface|\PHPUnit_Framework_MockObject_MockObject $venueMock */
        $venueMock = $this->createPartialMock(Venue::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $venueMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->venueData['id']);

        $this->assertSame($venueMock, $this->model->save($venueMock));
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

        /** @var VenueInterface|\PHPUnit_Framework_MockObject_MockObject $venueMock */
        $venueMock = $this->createPartialMock(Venue::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(CouldNotSaveException::class);
        $this->model->save($venueMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $storeId = 1;
        $venueId = 1;

        /** @var VenueInterface|\PHPUnit_Framework_MockObject_MockObject $venueMock */
        $venueMock = $this->createMock(Venue::class);
        $this->venueInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($venueMock);

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
            ->with($venueMock, $venueId)
            ->willReturnSelf();
        $venueMock->expects($this->once())
            ->method('getId')
            ->willReturn($venueId);

        $this->assertSame($venueMock, $this->model->get($venueId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with venueId = 1
     */
    public function testGetOnException()
    {
        $storeId = 1;
        $venueId = 1;

        /** @var VenueInterface|\PHPUnit_Framework_MockObject_MockObject $venueMock */
        $venueMock = $this->createMock(Venue::class);
        $this->venueInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($venueMock);

        $this->resourceMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($venueMock, $venueId)
            ->willReturnSelf();
        $venueMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->model->get($venueId, $storeId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $storeId = 1;
        $collectionSize = 1;
        /** @var VenueCollection|\PHPUnit_Framework_MockObject_MockObject $venueCollectionMock */
        $venueCollectionMock = $this->createPartialMock(
            VenueCollection::class,
            ['getSize', 'getItems', 'setStoreId']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(VenueSearchResultsInterface::class);
        /** @var Venue|\PHPUnit_Framework_MockObject_MockObject $venueModelMock */
        $venueModelMock = $this->createPartialMock(Venue::class, ['getData']);
        /** @var VenueInterface|\PHPUnit_Framework_MockObject_MockObject $venueMock */
        $venueMock = $this->getMockForAbstractClass(VenueInterface::class);

        $this->venueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($venueCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($venueCollectionMock, VenueInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $venueCollectionMock);

        $venueCollectionMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();

        $venueCollectionMock->expects($this->once())
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

        $venueCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$venueModelMock]);

        $this->venueInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($venueMock);
        $venueModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->venueData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($venueMock, $this->venueData, VenueInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$venueMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock, $storeId));
    }
}
