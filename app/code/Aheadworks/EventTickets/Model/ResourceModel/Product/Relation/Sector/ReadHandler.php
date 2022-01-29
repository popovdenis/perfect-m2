<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\Sector;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Aheadworks\EventTickets\Model\ResourceModel\Product\SectorRepository as ProductSectorRepository;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver\TicketPrice as TicketPriceResolver;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\Sector
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var HydratorPool
     */
    private $hydratorPool;

    /**
     * @var ProductSectorInterfaceFactory
     */
    private $productSectorFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ProductSectorRepository
     */
    private $productSectorRepository;

    /**
     * @var TicketPriceResolver
     */
    private $ticketPriceResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param HydratorPool $hydratorPool
     * @param ProductSectorInterfaceFactory $productSectorFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ProductSectorRepository $productSectorRepository
     * @param TicketPriceResolver $ticketPriceResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        HydratorPool $hydratorPool,
        ProductSectorInterfaceFactory $productSectorFactory,
        DataObjectHelper $dataObjectHelper,
        ProductSectorRepository $productSectorRepository,
        TicketPriceResolver $ticketPriceResolver
    ) {
        $this->metadataPool = $metadataPool;
        $this->hydratorPool = $hydratorPool;
        $this->productSectorFactory = $productSectorFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->productSectorRepository = $productSectorRepository;
        $this->ticketPriceResolver = $ticketPriceResolver;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getTypeId() !== EventTicket::TYPE_CODE) {
            return $entity;
        }

        $productSectors = $this->productSectorRepository->getByProductId($entity->getId());
        $productSectors = $this->prepareSectorTickets($entity, $productSectors);
        $extension = $entity->getExtensionAttributes();
        $extension->setAwEtSectorConfig($this->prepareExtensionAttributes($productSectors));
        $entity->setExtensionAttributes($extension);

        $hydrator = $this->hydratorPool->getHydrator(ProductInterface::class);
        $entityData = $hydrator->extract($entity);
        $entityData[ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG] = $productSectors;
        $entity = $hydrator->hydrate($entity, $entityData);
        return $entity;
    }

    /**
     * Prepare sector tickets
     *
     * @param Product $entity
     * @param array $productSectors
     * @return array
     */
    private function prepareSectorTickets($entity, &$productSectors)
    {
        foreach ($productSectors as &$productSector) {
            if (isset($productSector[ProductSectorInterface::SECTOR_TICKETS])) {
                foreach ($productSector[ProductSectorInterface::SECTOR_TICKETS] as &$ticket) {
                    $ticket[ProductSectorTicketInterface::FINAL_PRICE]
                        = $this->ticketPriceResolver->resolve($entity, $ticket);
                }
            }
        }

        return $productSectors;
    }

    /**
     * Prepare extension attributes for entity
     *
     * @param array $productSectors
     * @return ProductSectorInterface[]
     */
    private function prepareExtensionAttributes($productSectors)
    {
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
