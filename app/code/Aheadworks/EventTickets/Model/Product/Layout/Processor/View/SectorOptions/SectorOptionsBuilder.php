<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\SectorBuilder;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderInterfaceFactory;

/**
 * Class SectorOptionsBuilder
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions
 */
class SectorOptionsBuilder
{
    /**
     * @var SectorRenderInterfaceFactory
     */
    private $sectorRenderFactory;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var SectorBuilder
     */
    private $sectorBuilder;

    /**
     * @param SectorRenderInterfaceFactory $sectorRenderFactory
     * @param Resolver $resolver
     * @param SectorBuilder $sectorBuilder
     */
    public function __construct(
        SectorRenderInterfaceFactory $sectorRenderFactory,
        Resolver $resolver,
        SectorBuilder $sectorBuilder
    ) {
        $this->sectorRenderFactory = $sectorRenderFactory;
        $this->resolver = $resolver;
        $this->sectorBuilder = $sectorBuilder;
    }

    /**
     * Build sector render config
     *
     * @param Product $product
     * @return SectorRenderInterface[]
     */
    public function build($product)
    {
        $sectorConfig = $product->getTypeInstance()->getSectorConfig($product);
        $sectorRenders = [];
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            if ($this->isSectorAvailable($product, $sector->getSectorId())) {
                /** @var SectorRenderInterface $sectorRender */
                $sectorRender = $this->sectorRenderFactory->create();
                $sectorRenders[] = $this->sectorBuilder->build($product, $sector, $sectorRender);
            }
        }

        return $sectorRenders;
    }

    /**
     * Check if sector available to display
     *
     * @param Product $product
     * @param int $sectorId
     * @return bool
     */
    private function isSectorAvailable($product, $sectorId)
    {
        $preConfigSectorId = $this->resolver
            ->resolvePreconfiguredOptionValue($product, OptionInterface::BUY_REQUEST_SECTOR_ID);
        $isConfigure = $this->resolver->isConfigureProduct($product);

        return !$isConfigure || ($isConfigure && $preConfigSectorId == $sectorId);
    }
}
