<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\Sector;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\SectorRepository as ProductSectorRepository;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\Sector
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ProductSectorRepository
     */
    private $productSectorRepository;

    /**
     * @param ProductSectorRepository $productSectorRepository
     */
    public function __construct(
        ProductSectorRepository $productSectorRepository
    ) {
        $this->productSectorRepository = $productSectorRepository;
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getTypeId() !== EventTicket::TYPE_CODE) {
            return $entity;
        }

        $entityId = $entity->getId();
        $productSectors = !empty($entity->getExtensionAttributes()->getAwEtSectorConfig())
            ? $entity->getExtensionAttributes()->getAwEtSectorConfig()
            : [];
        $this->productSectorRepository->deleteByProductId($entityId);
        $this->productSectorRepository->save($productSectors, $entity);

        return $entity;
    }
}
