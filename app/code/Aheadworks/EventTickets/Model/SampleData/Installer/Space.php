<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterfaceFactory;
use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Aheadworks\EventTickets\Model\SampleData\Installer\Space\Sector as SectorReader;
use Aheadworks\EventTickets\Model\SampleData\Reader;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Store\Model\Store;

/**
 * Class Space
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer
 */
class Space implements SampleDataInstallerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var StorefrontLabelsInterfaceFactory
     */
    private $storefrontLabelsFactory;

    /**
     * @var SpaceInterfaceFactory
     */
    private $spaceDataFactory;

    /**
     * @var SpaceRepositoryInterface
     */
    private $spaceRepository;

    /**
     * @var SectorReader
     */
    private $sectorReader;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_EventTickets::fixtures/spaces.csv';

    /**
     * @param Reader $reader
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     * @param SpaceInterfaceFactory $spaceDataFactory
     * @param SpaceRepositoryInterface $spaceRepository
     * @param SectorReader $sectorReader
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Reader $reader,
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory,
        SpaceInterfaceFactory $spaceDataFactory,
        SpaceRepositoryInterface $spaceRepository,
        SectorReader $sectorReader,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->reader = $reader;
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
        $this->spaceDataFactory = $spaceDataFactory;
        $this->spaceRepository = $spaceRepository;
        $this->sectorReader = $sectorReader;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $sectorId => $row) {
            if (!$this->ifExists($row[SpaceInterface::NAME])) {
                $this->createSpace($sectorId, $row);
            }
        }
    }

    /**
     * Check if exists
     *
     * @param string $name
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function ifExists($name)
    {
        $this->searchCriteriaBuilder
            ->addFilter(SpaceInterface::NAME, $name)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $spaces = $this->spaceRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($spaces) > 0;
    }

    /**
     * Create space
     *
     * @param int $sectorId
     * @param array $row
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createSpace($sectorId, $row)
    {
        /** @var StorefrontLabelsInterface $currentLabels */
        $currentLabels = $this->storefrontLabelsFactory->create();
        $currentLabels
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->setTitle($row[StorefrontLabelsInterface::TITLE]);

        /** @var SpaceInterface $space */
        $space = $this->spaceDataFactory->create();
        $space
            ->setName($row[SpaceInterface::NAME])
            ->setStatus($row[SpaceInterface::STATUS])
            ->setVenueId($row[SpaceInterface::VENUE_ID])
            ->setSectors($row[SpaceInterface::VENUE_ID])
            ->setLabels([$currentLabels])
            ->setSectors($this->sectorReader->getSectorBySpaceId($sectorId));

        $this->spaceRepository->save($space);
    }
}
