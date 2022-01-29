<?php
namespace Aheadworks\EventTickets\Model\Product\Sector\Calculator;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\Status as TicketStatus;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Ticket
 *
 * @package Aheadworks\EventTickets\Model\Product\Sector\Calculator
 */
class Ticket
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array
     */
    private $ticketsQtyCache;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param SectorRepositoryInterface $sectorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        SectorRepositoryInterface $sectorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->sectorRepository = $sectorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve number ticket available
     *
     * @param $productId
     * @param $sectorId
     * @return int
     */
    public function getQtyAvailable($productId, $sectorId)
    {
        $sectorCapacity = $this->getSectorCapacity($sectorId);
        $usedTicketsQty = $this->getUsedTicketsQty($productId, $sectorId);
        $qtyAvailable = ($sectorCapacity >= $usedTicketsQty) ? ($sectorCapacity - $usedTicketsQty) : 0;
        return $qtyAvailable;
    }

    /**
     * Retrieve qty of tickets in the sector
     *
     * @param int $sectorId
     * @return int
     */
    private function getSectorCapacity($sectorId)
    {
        $sectorCapacity = 0;
        try {
            /** @var SectorInterface $sector */
            $sector = $this->sectorRepository->get($sectorId);
            $sectorCapacity = $sector->getTicketsQty();
        } catch (\Exception $exception) {
        }
        return $sectorCapacity;
    }

    /**
     * @param int $productId
     * @param int $sectorId
     * @return int
     */
    private function getUsedTicketsQty($productId, $sectorId)
    {
        try {
            if (!isset($this->ticketsQtyCache[$sectorId][$productId])) {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter(TicketInterface::SECTOR_ID, $sectorId)
                    ->addFilter(TicketInterface::PRODUCT_ID, $productId)
                    ->addFilter(TicketInterface::STATUS, TicketStatus::CANCELED, 'neq')
                    ->create();
                $usedTickets = $this->ticketRepository->getList($searchCriteria);
                $this->ticketsQtyCache[$sectorId][$productId] = $usedTickets->getTotalCount();
            }
        } catch (\Exception $exception) {
            return 0;
        }

        return $this->ticketsQtyCache[$sectorId][$productId];
    }
}
