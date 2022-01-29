<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Magento\Framework\Escaper;

/**
 * Class Sector
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Renderer
 */
class Sector implements RendererInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @param Escaper $escaper
     * @param SectorRepositoryInterface $sectorRepository
     */
    public function __construct(
        Escaper $escaper,
        SectorRepositoryInterface $sectorRepository
    ) {
        $this->escaper = $escaper;
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function render($options)
    {
        $result = [];
        if (!$options->getAwEtSectorId()) {
            return $result;
        }

        $sectorLabel = $this->getSectorName($options->getAwEtSectorId());
        $result[] = [
            'label' => __('Sector'),
            'value' => $this->escaper->escapeHtml($sectorLabel)
        ];

        return $result;
    }

    /**
     * Retrieve sector name
     *
     * @param int $sectorId
     * @return string
     */
    private function getSectorName($sectorId)
    {
        $sectorName = '';
        try {
            /** @var SectorInterface $sector */
            $sector = $this->sectorRepository->get($sectorId);
            $sectorName = $sector->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
        }
        return $sectorName;
    }
}
