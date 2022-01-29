<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterfaceFactory;
use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Aheadworks\EventTickets\Model\SampleData\Reader;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Store\Model\Store;

/**
 * Class Venue
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer
 */
class Venue implements SampleDataInstallerInterface
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
     * @var VenueInterfaceFactory
     */
    private $venueDataFactory;

    /**
     * @var VenueRepositoryInterface
     */
    private $venueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_EventTickets::fixtures/venues.csv';

    /**
     * @param Reader $reader
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     * @param VenueInterfaceFactory $venueDataFactory
     * @param VenueRepositoryInterface $venueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Reader $reader,
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory,
        VenueInterfaceFactory $venueDataFactory,
        VenueRepositoryInterface $venueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->reader = $reader;
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
        $this->venueDataFactory = $venueDataFactory;
        $this->venueRepository = $venueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->ifExists($row[VenueInterface::NAME])) {
                $this->createVenue($row);
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
            ->addFilter(VenueInterface::NAME, $name)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $venues = $this->venueRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($venues) > 0;
    }

    /**
     * Create venue
     *
     * @param array $row
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createVenue($row)
    {
        /** @var StorefrontLabelsInterface $currentLabels */
        $currentLabels = $this->storefrontLabelsFactory->create();
        $currentLabels
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->setTitle($row[StorefrontLabelsInterface::TITLE]);

        /** @var VenueInterface $venue */
        $venue = $this->venueDataFactory->create();
        $venue
            ->setName($row[VenueInterface::NAME])
            ->setStatus($row[VenueInterface::STATUS])
            ->setAddress($row[VenueInterface::ADDRESS])
            ->setLabels([$currentLabels]);

        $this->venueRepository->save($venue);
    }
}
