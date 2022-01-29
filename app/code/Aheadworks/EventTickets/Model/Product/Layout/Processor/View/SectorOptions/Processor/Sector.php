<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor;

use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Resolver;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;

/**
 * Class Sector
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor
 */
class Sector implements SectorBuilderProcessorInterface
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @param Resolver $resolver
     * @param StockManagementInterface $stockManagement
     */
    public function __construct(
        Resolver $resolver,
        StockManagementInterface $stockManagement
    ) {
        $this->resolver = $resolver;
        $this->stockManagement = $stockManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $sectorRender)
    {
        $sectorId = $sector->getSectorId();
        $productId = $product->getId();
        $status = $this->stockManagement->getTicketSectorStatus($productId, $sectorId);
        $qtyAvailable = $this->stockManagement->getAvailableTicketQtyBySector($productId, $sectorId);
        $statusLabel = $product->getAwEtScheduleType() == ScheduleType::RECURRING
            ? ''
            : $this->resolver->resolveStockStatusLabel($status, $qtyAvailable);

        $sectorRender
            ->setId($sectorId)
            ->setName($this->resolver->resolveSectorName($sectorId))
            ->setDescription($this->resolver->resolveSectorDescription($sectorId))
            ->setQtyAvailable($qtyAvailable)
            ->setStatus($statusLabel)
            ->setIsSalable($this->stockManagement->isSalableBySector($productId, $sectorId))
            ->setPriceRange('')
            ->setTickets([])
            ->setIsConfigurePage($this->resolver->isConfigureProduct($product));

        return $sectorRender;
    }
}
