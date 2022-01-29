<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Generator;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Sku
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Generator
 */
class Sku
{
    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @param SectorRepositoryInterface $sectorRepository
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     */
    public function __construct(
        SectorRepositoryInterface $sectorRepository,
        TicketTypeRepositoryInterface $ticketTypeRepository
    ) {
        $this->sectorRepository = $sectorRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
    }

    /**
     * Generate sku
     *
     * @param string $sku
     * @param Product $product
     * @return string
     */
    public function generate($sku, $product)
    {
        $skuParts = [$sku];
        if ($product->hasCustomOptions()) {
            if ($sectorSku = $this->getSectorSku($product)) {
                $skuParts[] = $sectorSku;
            }
            if ($ticketTypeSku = $this->getTicketTypeSku($product)) {
                $skuParts[] = $ticketTypeSku;
            }
        }

        return implode('-', $skuParts);
    }

    /**
     * Retrieve sector sku
     *
     * @param Product $product
     * @return string|bool
     */
    private function getSectorSku($product)
    {
        $entityId = $product->getCustomOption(OptionInterface::SECTOR_ID)->getValue();
        try {
            $sector = $this->sectorRepository->get($entityId, $product->getStoreId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $this->prepareSku($sector->getSku());
    }

    /**
     * Retrieve ticket type sku
     *
     * @param Product $product
     * @return string|bool
     */
    private function getTicketTypeSku($product)
    {
        $entityId = $product->getCustomOption(OptionInterface::TICKET_TYPE_ID)->getValue();
        try {
            $ticketType = $this->ticketTypeRepository->get($entityId, $product->getStoreId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $this->prepareSku($ticketType->getSku());
    }

    /**
     * Prepare sku
     *
     * @param string $sku
     * @return string|bool
     */
    private function prepareSku($sku)
    {
        $sku = trim($sku);

        return !empty($sku) ? $sku : false;
    }
}
