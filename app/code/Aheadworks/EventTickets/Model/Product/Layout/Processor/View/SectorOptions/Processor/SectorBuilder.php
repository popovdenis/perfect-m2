<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor;

/**
 * Class SectorBuilder
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor
 */
class SectorBuilder
{
    /**
     * @var SectorBuilderProcessorInterface[]
     */
    private $processors = [];

    /**
     * @param SectorBuilderProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $sectorRender)
    {
        foreach ($this->processors as $processor) {
            $processor->build($product, $sector, $sectorRender);
        }
        return $sectorRender;
    }
}
