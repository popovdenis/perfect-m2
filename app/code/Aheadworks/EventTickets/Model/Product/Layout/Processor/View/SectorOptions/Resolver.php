<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Aheadworks\EventTickets\Model\Source\Product\Stock\Status as StockStatusSource;
use Magento\Catalog\Model\Product;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions
 */
class Resolver
{
    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var StockStatusSource
     */
    private $stockStatusSource;

    /**
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param SectorRepositoryInterface $sectorRepository
     * @param StockStatusSource $stockStatusSource
     */
    public function __construct(
        TicketTypeRepositoryInterface $ticketTypeRepository,
        SectorRepositoryInterface $sectorRepository,
        StockStatusSource $stockStatusSource
    ) {
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->sectorRepository = $sectorRepository;
        $this->stockStatusSource = $stockStatusSource;
    }

    /**
     * Retrieve sector name
     *
     * @param int $sectorId
     * @return string
     */
    public function resolveSectorName($sectorId)
    {
        $sectorName = '';
        try {
            $sector = $this->sectorRepository->get($sectorId);
            $sectorName = $sector->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
        }
        return $sectorName;
    }

    /**
     * Retrieve sector description
     *
     * @param int $sectorId
     * @return string
     */
    public function resolveSectorDescription($sectorId)
    {
        $sectorDescription = '';
        try {
            $sector = $this->sectorRepository->get($sectorId);
            $sectorDescription = $sector->getCurrentLabels()->getDescription();
        } catch (\Exception $exception) {
        }
        return $sectorDescription;
    }

    /**
     * Retrieve ticket type label
     *
     * @param int $ticketTypeId
     * @return string
     */
    public function resolveTicketTypeLabel($ticketTypeId)
    {
        $ticketTypeLabel = '';
        try {
            $ticketType = $this->ticketTypeRepository->get($ticketTypeId);
            $ticketTypeLabel = $ticketType->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
        }
        return $ticketTypeLabel;
    }

    /**
     * Retrieve ticket type description
     *
     * @param int $ticketTypeId
     * @return string
     */
    public function resolveTicketTypeDescription($ticketTypeId)
    {
        $ticketTypeLabel = '';
        try {
            $ticketType = $this->ticketTypeRepository->get($ticketTypeId);
            $ticketTypeLabel = $ticketType->getCurrentLabels()->getDescription();
        } catch (\Exception $exception) {
        }
        return $ticketTypeLabel;
    }

    /**
     * Retrieve stock status label
     *
     * @param int $status
     * @param int $qty
     * @return string
     */
    public function resolveStockStatusLabel($status, $qty)
    {
        switch ($status) {
            case StockStatusSource::AVAILABLE:
                $statusLabel = __('Available: %1', $qty);
                break;
            case StockStatusSource::CAPACITY:
                $statusLabel = __('Capacity: %1', $qty);
                break;
            default:
                $statusLabel = $this->stockStatusSource->getOptionLabelByValue($status);
                break;
        }

        return $statusLabel;
    }

    /**
     * Retrieves preconfigured option value by code
     *
     * @param Product $product
     * @param string $code
     * @param string $default
     * @return mixed|string
     */
    public function resolvePreconfiguredOptionValue($product, $code, $default = '')
    {
        $value = $product->getPreconfiguredValues()->getData($code);
        return $value ? $value : $default;
    }

    /**
     * Check if configure product
     *
     * @param Product $product
     * @return bool
     */
    public function isConfigureProduct($product)
    {
        return (bool)$this->resolvePreconfiguredOptionValue(
            $product,
            OptionInterface::BUY_REQUEST_PRODUCT_IS_CONFIGURE,
            false
        );
    }
}
