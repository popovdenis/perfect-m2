<?php
namespace Aheadworks\EventTickets\Model\Import\Processor;

use Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface;
use Aheadworks\EventTickets\Model\Import\Processor\SectorConfig\Product;
use Aheadworks\EventTickets\Model\ResourceModel\Product\SectorRepository as ProductSectorRepository;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig as SectorConfigRowCustomizer;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Model\Import\Processor\SectorConfig\TicketType as TicketTypeProcessor;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig\Product as ProductRowCustomizer;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

/**
 * Class SectorConfig
 *
 * @package Aheadworks\EventTickets\Model\Import\Processor
 */
class SectorConfig implements ProcessorInterface
{
    /**
     * @var ProductSectorRepository
     */
    private $productSectorRepository;

    /**
     * @var ProductSectorInterfaceFactory
     */
    private $productSectorFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TicketTypeProcessor
     */
    private $ticketTypeProcessor;

    /**
     * @var Product
     */
    private $productProcessor;

    /**
     * @param ProductSectorRepository $productSectorRepository
     * @param ProductSectorInterfaceFactory $productSectorFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param SectorRepositoryInterface $sectorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TicketTypeProcessor $ticketTypeProcessor
     * @param Product $productProcessor
     */
    public function __construct(
        ProductSectorRepository $productSectorRepository,
        ProductSectorInterfaceFactory $productSectorFactory,
        DataObjectHelper $dataObjectHelper,
        SectorRepositoryInterface $sectorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TicketTypeProcessor $ticketTypeProcessor,
        Product $productProcessor
    ) {
        $this->productSectorRepository = $productSectorRepository;
        $this->productSectorFactory = $productSectorFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->sectorRepository = $sectorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ticketTypeProcessor = $ticketTypeProcessor;
        $this->productProcessor = $productProcessor;
    }

    /**
     * Process data
     *
     * @param array $rowData
     * @param \Magento\Catalog\Model\Product $entity
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processData($rowData, $entity)
    {
        $entityId = $entity->getEntityId();
        $sectorTitles = isset($rowData[SectorConfigRowCustomizer::SECTOR_COLUMN_ID])
            ? $rowData[SectorConfigRowCustomizer::SECTOR_COLUMN_ID]
            : "";
        $sectorTitlesArray = explode(SectorConfigRowCustomizer::SECTORS_SEPARATOR, $sectorTitles);
        $products = $this->getProductsData($rowData);

        $sectorsData = [];
        foreach ($sectorTitlesArray as $sectorIndex => $sectorTitle) {
            $productSectorConfigArray = [];
            if (!empty($sectorTitle)) {
                $sector = $this->getSectorByTitle($sectorTitle);
                if (isset($sector)) {
                    $productSectorConfigArray[ProductSectorInterface::SECTOR_ID] = $sector->getId();
                    $productSectorConfigArray[ProductSectorInterface::PRODUCT_ID] = $entityId;
                    $productSectorConfigArray[ProductSectorInterface::SECTOR_PRODUCTS] = isset($products[$sectorIndex])
                        ? $this->productProcessor->changeProductSkuToId($products[$sectorIndex])
                        : [];
                    $sectorsData[] = $productSectorConfigArray;
                }
            }
        }

        $sectorsData = $this->ticketTypeProcessor->processData($entity, $rowData, $sectorsData);

        $productSectorObjects = [];
        foreach ($sectorsData as $sectorsDataRow) {
            $productSectorDataObject = $this->productSectorFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productSectorDataObject,
                $sectorsDataRow,
                ProductSectorInterface::class
            );
            $productSectorObjects[] = $productSectorDataObject;
        }

        $this->productSectorRepository->deleteByProductId($entityId);
        $this->productSectorRepository->save($productSectorObjects, $entity);

        $entity->getExtensionAttributes()->setAwEtSectorConfig($productSectorObjects);

        return $rowData;
    }

    /**
     * Retrieve products data
     *
     * @param array $rowData
     * @return array
     */
    private function getProductsData($rowData)
    {
        $productIds = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[ProductRowCustomizer::SECTOR_PRODUCT_SKU_COLUMN_ID])
                ? $rowData[ProductRowCustomizer::SECTOR_PRODUCT_SKU_COLUMN_ID]
                : ""
        );
        $productPositions = explode(
            SectorConfigRowCustomizer::SECTORS_SEPARATOR,
            isset($rowData[ProductRowCustomizer::SECTOR_PRODUCT_POSITION_COLUMN_ID])
                ? $rowData[ProductRowCustomizer::SECTOR_PRODUCT_POSITION_COLUMN_ID]
                : ""
        );

        $products = array_combine(
            array_keys($productIds),
            array_map(
                function ($productId, $productPosition) {
                    if (empty($productId)) {
                        return [];
                    }

                    $productId = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $productId);
                    $productPosition = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $productPosition);

                    $products = array_combine(
                        array_keys($productId),
                        array_map(
                            function ($productId, $productPosition) {
                                if (empty($productId) || empty($productPosition)) {
                                    return [];
                                }
                                return [
                                    ProductSectorProductInterface::PRODUCT_ID => $productId,
                                    ProductSectorProductInterface::POSITION => $productPosition
                                ];
                            },
                            $productId,
                            $productPosition
                        )
                    );
                    return $products;
                },
                $productIds,
                $productPositions
            )
        );

        return $products;
    }

    /**
     * Retrieve sector by title
     *
     * @param string $sectorTitle
     * @return SectorInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSectorByTitle($sectorTitle)
    {
        $this->searchCriteriaBuilder
            ->addFilter(SectorInterface::NAME, $sectorTitle)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $sectors = $this->sectorRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($sectors) > 0 ? reset($sectors) : null;
    }
}
