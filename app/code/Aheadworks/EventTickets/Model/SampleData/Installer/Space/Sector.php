<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer\Space;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterfaceFactory;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Model\SampleData\Reader;
use Magento\Store\Model\Store;

/**
 * Class Sector
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer
 */
class Sector
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
     * @var SectorInterfaceFactory
     */
    private $sectorDataFactory;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var array|null
     */
    private $rows;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_EventTickets::fixtures/sectors.csv';

    /**
     * @param Reader $reader
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     * @param SectorInterfaceFactory $sectorDataFactory
     * @param SectorRepositoryInterface $sectorRepository
     */
    public function __construct(
        Reader $reader,
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory,
        SectorInterfaceFactory $sectorDataFactory,
        SectorRepositoryInterface $sectorRepository
    ) {
        $this->reader = $reader;
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
        $this->sectorDataFactory = $sectorDataFactory;
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * Retrieve sector by space id
     *
     * @param int $spaceId
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSectorBySpaceId($spaceId)
    {
        $sectors = [];
        foreach ($this->getRows() as $row) {
            if ($row[SectorInterface::SPACE_ID] == $spaceId) {
                $sectors[] = $this->createSectorObject($row);
            }
        }
        return $sectors;
    }

    /**
     * Retrieve rows
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRows()
    {
        if (null === $this->rows) {
            $this->rows = $this->reader->readFile($this->fileName);
        }
        return $this->rows;
    }

    /**
     * Create sector object
     *
     * @param array $row
     * @return SectorInterface
     */
    private function createSectorObject($row)
    {
        /** @var StorefrontLabelsInterface $currentLabels */
        $currentLabels = $this->storefrontLabelsFactory->create();
        $currentLabels
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->setTitle($row[StorefrontLabelsInterface::TITLE]);

        /** @var SectorInterface $sector */
        $sector = $this->sectorDataFactory->create();
        $sector
            ->setName($row[SectorInterface::NAME])
            ->setStatus($row[SectorInterface::STATUS])
            ->setSku($row[SectorInterface::SKU])
            ->setTicketsQty($row[SectorInterface::TICKETS_QTY])
            ->setSortOrder($row[SectorInterface::SORT_ORDER])
            ->setLabels([$currentLabels]);

        return $sector;
    }
}
