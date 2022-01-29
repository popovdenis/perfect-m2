<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

/**
 * Class ProductBuilder
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
class ProductBuilder
{
    /**
     * @var ProductBuilderProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProductBuilderProcessorInterface[] $processors
     */
    public function __construct(
        array $processors
    ) {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $additionalProductRender)
    {
        foreach ($this->processors as $processor) {
            $processor->build($product, $additionalProductRender);
        }
        return $additionalProductRender;
    }
}
