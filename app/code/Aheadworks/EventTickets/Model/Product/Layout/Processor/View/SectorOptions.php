<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\SectorBuilder;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\SectorOptionsBuilder;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render\Hydrator;

/**
 * Class SectorOptions
 *
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View
 */
class SectorOptions implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var SectorOptionsBuilder
     */
    private $sectorOptionsBuilder;

    /**
     * @var Hydrator
     */
    private $hydrator;

    /**
     * @param ArrayManager $arrayManager
     * @param SectorOptionsBuilder $sectorOptionsBuilder
     * @param Hydrator $hydrator
     */
    public function __construct(
        ArrayManager $arrayManager,
        SectorOptionsBuilder $sectorOptionsBuilder,
        Hydrator $hydrator
    ) {
        $this->arrayManager = $arrayManager;
        $this->sectorOptionsBuilder = $sectorOptionsBuilder;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $product)
    {
        $optionsProviderPath = 'components/awEtViewOptionsProvider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            [
                'data' => [
                    'sectorConfig' => $this->extractDataFromObject($this->sectorOptionsBuilder->build($product))
                ]
            ]
        );

        return $jsLayout;
    }

    /**
     * Extract data from object
     *
     * @param SectorRenderInterface[] $sectorObjects
     * @return array
     */
    private function extractDataFromObject($sectorObjects)
    {
        $sectors = [];
        foreach ($sectorObjects as $sectorObject) {
            $sectors[] = $this->hydrator->extract($sectorObject);
        }

        return $sectors;
    }
}
