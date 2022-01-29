<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute;

use Aheadworks\EventTickets\Model\ResourceModel\Product\SectorRepository as ProductSectorRepository;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig\TicketType;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig\Product;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;

/**
 * Class SectorConfig
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute
 */
class SectorConfig implements CustomizerInterface
{
    /**#@+
     * Constants defined for names of corresponding columns
     */
    const SECTOR_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector';
    /**#@-*/

    /**
     * Symbol between sectors names and its configuration parameters.
     */
    const SECTORS_SEPARATOR = "\n";

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
     * @var TicketType
     */
    private $ticketTypeCustomizer;

    /**
     * @var Product
     */
    private $productCustomizer;

    /**
     * @var AttributeFormatterPool
     */
    private $attributeFormatterPool;

    /**
     * @var array
     */
    private $sectorConfigColumns = [
        self::SECTOR_COLUMN_ID
    ];

    /**
     * @param ProductSectorRepository $productSectorRepository
     * @param ProductSectorInterfaceFactory $productSectorFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketType $ticketTypeCustomizer
     * @param Product $productCustomizer
     * @param AttributeFormatterPool $attributeFormatterPool
     */
    public function __construct(
        ProductSectorRepository $productSectorRepository,
        ProductSectorInterfaceFactory $productSectorFactory,
        DataObjectHelper $dataObjectHelper,
        TicketType $ticketTypeCustomizer,
        Product $productCustomizer,
        AttributeFormatterPool $attributeFormatterPool
    ) {
        $this->productSectorRepository = $productSectorRepository;
        $this->productSectorFactory = $productSectorFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketTypeCustomizer = $ticketTypeCustomizer;
        $this->productCustomizer = $productCustomizer;
        $this->attributeFormatterPool = $attributeFormatterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($eventTicketsAttributesProductsData)
    {
        $preparedData = [];
        $sectorFormatter = $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '/sector/sector_id'
        );
        try {
            foreach ($eventTicketsAttributesProductsData as $productId => $productData) {
                $productSectorObjects = $this->getProductSectorsObjects($productId);

                $preparedProductData = $productData;
                $sectorColumnValue = '';
                /** @var ProductSectorInterface $productSectorObject */
                foreach ($productSectorObjects as $productSectorObject) {
                    $sectorColumnValue = $sectorColumnValue
                        . $sectorFormatter->getFormattedValue($productSectorObject->getSectorId())
                        . self::SECTORS_SEPARATOR;
                }

                $preparedProductData = array_merge(
                    $preparedProductData,
                    $this->ticketTypeCustomizer->getPreparedProductData($productSectorObjects),
                    $this->productCustomizer->getPreparedProductData($productSectorObjects)
                );

                $preparedProductData[self::SECTOR_COLUMN_ID]
                    = $sectorColumnValue;

                $preparedData[$productId] = $preparedProductData;
            }
        } catch (\Exception $exception) {
        }

        return $preparedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderColumns()
    {
        return array_merge(
            $this->sectorConfigColumns,
            $this->ticketTypeCustomizer->getHeaderColumns(),
            $this->productCustomizer->getHeaderColumns()
        );
    }

    /**
     * Retrieve product sectors objects
     *
     * @param int $productId
     * @return ProductSectorInterface[]
     * @throws \Exception
     */
    private function getProductSectorsObjects($productId)
    {
        $productSectors = $this->productSectorRepository->getByProductId($productId);
        $productSectorObjects = [];
        foreach ($productSectors as $productSector) {
            $productSectorDataObject = $this->productSectorFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productSectorDataObject,
                $productSector,
                ProductSectorInterface::class
            );
            $productSectorObjects[] = $productSectorDataObject;
        }
        return $productSectorObjects;
    }
}
