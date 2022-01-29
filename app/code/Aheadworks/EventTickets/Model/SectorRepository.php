<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SectorSearchResultsInterface;
use Aheadworks\EventTickets\Api\Data\SectorSearchResultsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Sector as SectorResourceModel;
use Aheadworks\EventTickets\Model\ResourceModel\Sector\CollectionFactory as SectorCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\StorefrontLabelsEntity\AbstractRepository as StorefrontLabelsEntityAbstractRepository;

/**
 * Class SectorRepository
 *
 * @package Aheadworks\EventTickets\Model
 */
class SectorRepository extends StorefrontLabelsEntityAbstractRepository implements SectorRepositoryInterface
{
    /**
     * @var SectorResourceModel
     */
    private $resource;

    /**
     * @var SectorInterfaceFactory
     */
    private $sectorInterfaceFactory;

    /**
     * @var SectorCollectionFactory
     */
    private $sectorCollectionFactory;

    /**
     * @var SectorSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param SectorResourceModel $resource
     * @param SectorInterfaceFactory $sectorInterfaceFactory
     * @param SectorCollectionFactory $sectorCollectionFactory
     * @param SectorSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param StoreManagerInterface $storeManager
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        SectorResourceModel $resource,
        SectorInterfaceFactory $sectorInterfaceFactory,
        SectorCollectionFactory $sectorCollectionFactory,
        SectorSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        StoreManagerInterface $storeManager,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->sectorInterfaceFactory = $sectorInterfaceFactory;
        $this->sectorCollectionFactory = $sectorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function save(SectorInterface $sector)
    {
        try {
            $this->resource->save($sector);
            $this->registry[$sector->getId()] = $sector;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $sector;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sectorId, $storeId = null)
    {
        if (!isset($this->registry[$sectorId])) {
            /** @var SectorInterface $sector */
            $sector = $this->sectorInterfaceFactory->create();
            $this->applyStoreIdToObject($this->resource, $storeId);
            $this->resource->load($sector, $sectorId);
            if (!$sector->getId()) {
                throw NoSuchEntityException::singleField('sectorId', $sectorId);
            }
            $this->registry[$sectorId] = $sector;
        }
        return $this->registry[$sectorId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\EventTickets\Model\ResourceModel\Sector\Collection $collection */
        $collection = $this->sectorCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, SectorInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $this->applyStoreIdToObject($collection, $storeId);

        /** @var SectorSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Sector $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Sector $model
     * @return SectorInterface
     */
    private function getDataObject($model)
    {
        /** @var SectorInterface $object */
        $object = $this->sectorInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            SectorInterface::class
        );
        return $object;
    }
}
