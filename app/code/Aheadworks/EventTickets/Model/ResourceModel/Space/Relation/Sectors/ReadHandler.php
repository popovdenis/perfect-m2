<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Space\Relation\Sectors;

use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReadHandler
 * @package Aheadworks\EventTickets\Model\ResourceModel\Space\Relation\Sectors
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param SectorRepositoryInterface $sectorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        SectorRepositoryInterface $sectorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->sectorRepository = $sectorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var SpaceInterface $entity */
        if ($entityId = (int)$entity->getId()) {
            $sectors = $this->getSectorsForSpace($entityId, $arguments['store_id']);
            $entity->setSectors($sectors);
        }
        return $entity;
    }

    /**
     * Retrieve array of sectors, assigned to the space with specified id
     *
     * @param int $spaceId
     * @param int|null $storeId
     * @return SectorInterface[]|array
     */
    private function getSectorsForSpace($spaceId, $storeId)
    {
        try {
            $sortOrder = $this->sortOrderBuilder
                ->setField(SectorInterface::SORT_ORDER)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();
            $this->searchCriteriaBuilder
                ->addFilter(SectorInterface::SPACE_ID, $spaceId)
                ->addSortOrder($sortOrder);

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->sectorRepository->getList($searchCriteria, $storeId);
            $sectors = $searchResults->getItems();
        } catch (LocalizedException $exception) {
            $sectors = [];
        }
        return $sectors;
    }
}
