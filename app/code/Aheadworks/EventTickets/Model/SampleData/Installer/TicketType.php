<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterfaceFactory;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Aheadworks\EventTickets\Model\SampleData\Reader;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Store\Model\Store;

/**
 * Class TicketType
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer
 */
class TicketType implements SampleDataInstallerInterface
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
     * @var TicketTypeInterfaceFactory
     */
    private $ticketTypeDataFactory;

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_EventTickets::fixtures/ticket_types.csv';

    /**
     * @param Reader $reader
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     * @param TicketTypeInterfaceFactory $ticketTypeDataFactory
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Reader $reader,
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory,
        TicketTypeInterfaceFactory $ticketTypeDataFactory,
        TicketTypeRepositoryInterface $ticketTypeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->reader = $reader;
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
        $this->ticketTypeDataFactory = $ticketTypeDataFactory;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->ifExists($row[TicketTypeInterface::NAME])) {
                $this->createTicketType($row);
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
            ->addFilter(TicketTypeInterface::NAME, $name)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $ticketTypes = $this->ticketTypeRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($ticketTypes) > 0;
    }

    /**
     * Create ticket type
     *
     * @param array $row
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createTicketType($row)
    {
        /** @var StorefrontLabelsInterface $currentLabels */
        $currentLabels = $this->storefrontLabelsFactory->create();
        $currentLabels
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->setTitle($row[StorefrontLabelsInterface::TITLE]);

        /** @var TicketTypeInterface $ticketType */
        $ticketType = $this->ticketTypeDataFactory->create();
        $ticketType
            ->setName($row[TicketTypeInterface::NAME])
            ->setStatus($row[TicketTypeInterface::STATUS])
            ->setSku($row[TicketTypeInterface::SKU])
            ->setLabels([$currentLabels]);

        $this->ticketTypeRepository->save($ticketType);
    }
}
