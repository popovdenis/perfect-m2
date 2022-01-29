<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor;

use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderInterface;
use Magento\Catalog\Model\Product;

/**
 * Interface SectorBuilderProcessorInterface
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor
 */
interface SectorBuilderProcessorInterface
{
    /**
     * Build sector render object
     *
     * @param Product $product
     * @param ProductSectorInterface $sector
     * @param SectorRenderInterface $sectorRender
     * @return SectorRenderInterface
     */
    public function build($product, $sector, $sectorRender);
}
