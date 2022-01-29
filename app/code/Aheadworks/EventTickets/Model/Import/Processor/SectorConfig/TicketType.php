<?php
namespace Aheadworks\EventTickets\Model\Import\Processor\SectorConfig;

use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig\TicketType
    as TicketTypeRowCustomizer;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig as SectorConfigRowCustomizer;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\ImportExport\Model\Import;

/**
 * Class TicketType
 *
 * @package Aheadworks\EventTickets\Model\Import\Processor\SectorConfig
 */
class TicketType
{
    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        TicketTypeRepositoryInterface $ticketTypeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Process data
     *
     * @param ProductInterface $entity
     * @param array $rowData
     * @param array $sectorsData
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processData($entity, $rowData, $sectorsData)
    {
        $processedData = [];

        $ticketTypeTitlesGroupedBySector = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_COLUMN_ID])
                ? $rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_COLUMN_ID]
                : ""
        );
        $ticketTypeEarlyBirdPricesGroupedBySector = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_EARLY_BIRD_PRICE_COLUMN_ID])
                ? $rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_EARLY_BIRD_PRICE_COLUMN_ID]
                : ""
        );
        $ticketTypePricesGroupedBySector = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_PRICE_COLUMN_ID])
                ? $rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_PRICE_COLUMN_ID]
                : ""
        );
        $ticketTypeLastDaysPricesGroupedBySector = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_LAST_DAYS_PRICE_COLUMN_ID])
                ? $rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_LAST_DAYS_PRICE_COLUMN_ID]
                : ""
        );
        $ticketTypePositionsGroupedBySector = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_POSITION_COLUMN_ID])
                ? $rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_POSITION_COLUMN_ID]
                : ""
        );
        $ticketTypePersonalOptionsGroupedBySector = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_PERSONAL_OPTIONS_COLUMN_ID])
                ? $rowData[TicketTypeRowCustomizer::SECTOR_TICKET_TYPE_PERSONAL_OPTIONS_COLUMN_ID]
                : ""
        );

        foreach ($sectorsData as $sectorIndex => $sectorDataRow) {
            $processedSectorData = $sectorDataRow;

            $sectorTicketsTypesData = [];
            $sectorTicketTypeTitlesString = $ticketTypeTitlesGroupedBySector[$sectorIndex];
            $sectorTicketTypeEarlyBirdPricesString = $ticketTypeEarlyBirdPricesGroupedBySector[$sectorIndex];
            $sectorTicketTypePricesString = $ticketTypePricesGroupedBySector[$sectorIndex];
            $sectorTicketTypeLastDaysPricesString = $ticketTypeLastDaysPricesGroupedBySector[$sectorIndex];
            $sectorTicketTypePositionsString = $ticketTypePositionsGroupedBySector[$sectorIndex];
            $sectorTicketTypePersonalOptionsString = $ticketTypePersonalOptionsGroupedBySector[$sectorIndex];

            $ticketTypeTitles = explode(
                ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                $sectorTicketTypeTitlesString
            );
            $ticketTypeEarlyBirdPrices = explode(
                ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                $sectorTicketTypeEarlyBirdPricesString
            );
            $ticketTypePrices = explode(
                ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                $sectorTicketTypePricesString
            );
            $ticketTypeLastDaysPrices = explode(
                ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                $sectorTicketTypeLastDaysPricesString
            );
            $ticketTypePositions = explode(
                ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                $sectorTicketTypePositionsString
            );
            $ticketTypePersonalOptions = explode(
                ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                $sectorTicketTypePersonalOptionsString
            );
            foreach ($ticketTypeTitles as $typeIndex => $typeTitle) {
                $ticketTypeData = [];
                if (!empty($typeTitle)) {
                    $ticketType = $this->getTicketTypeByTitle($typeTitle);
                    if (isset($ticketType)) {
                        $ticketTypeData[ProductSectorTicketInterface::TYPE_ID] = $ticketType->getId();
                        $ticketTypeData[ProductSectorTicketInterface::EARLY_BIRD_PRICE]
                            = $ticketTypeEarlyBirdPrices[$typeIndex] ?: null;
                        $ticketTypeData[ProductSectorTicketInterface::PRICE] = $ticketTypePrices[$typeIndex];
                        $ticketTypeData[ProductSectorTicketInterface::LAST_DAYS_PRICE]
                            = $ticketTypeLastDaysPrices[$typeIndex] ?: null;
                        $ticketTypeData[ProductSectorTicketInterface::POSITION] = $ticketTypePositions[$typeIndex];
                        $personalOptions = explode(
                            Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                            $ticketTypePersonalOptions[$typeIndex]
                        );
                        $ticketTypeData[ProductSectorTicketInterface::PERSONAL_OPTION_UIDS] =
                            $this->getPersonalOptionUids($entity, $personalOptions);

                        $sectorTicketsTypesData[] = $ticketTypeData;
                    }
                }
            }
            $processedSectorData[ProductSectorInterface::SECTOR_TICKETS] = $sectorTicketsTypesData;
            $processedData[] = $processedSectorData;
        }
        return $processedData;
    }

    /**
     * Retrieve personal option uids
     *
     * @param Product $entity
     * @param array $personalOptions
     * @return array
     */
    private function getPersonalOptionUids($entity, $personalOptions)
    {
        $uids = [];
        foreach ($personalOptions as $option) {
            if ($uid = $this->getPersonalOptionUidByName($entity, $option)) {
                $uids[] = $uid;
            }
        }

        return $uids;
    }

    /**
     * Retrieve personal option uid by name
     *
     * @param Product $entity
     * @param string $option
     * @return int|null
     */
    private function getPersonalOptionUidByName($entity, $option)
    {
        $entityPersonalOptions = $entity->getExtensionAttributes()->getAwEtPersonalOptions();
        foreach ($entityPersonalOptions as $entityOption) {
            foreach ($entityOption->getLabels() as $label) {
                if ($label->getTitle() == $option) {
                    return $entityOption->getUid();
                }
            }
        }
        return null;
    }

    /**
     * @param string $ticketTypeTitle
     * @return TicketTypeInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTicketTypeByTitle($ticketTypeTitle)
    {
        $this->searchCriteriaBuilder
            ->addFilter(TicketTypeInterface::NAME, $ticketTypeTitle)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $ticketTypes = $this->ticketTypeRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($ticketTypes) > 0 ? reset($ticketTypes) : null;
    }
}
