<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Ticket\AvailableTicket;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Model\Product\Sector;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class Resolver
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Ticket\AvailableTicket
 */
class Resolver
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Ticket
     */
    private $ticketResource;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Ticket $ticketResource
     * @param SectorRepositoryInterface $sectorRepository
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Ticket $ticketResource,
        SectorRepositoryInterface $sectorRepository
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ticketResource = $ticketResource;
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * Get purchased tickets for recurring event on provided timeslot
     *
     * @param ProductRecurringScheduleInterface $recurringEvent
     * @param int|array|null $sectorId
     * @return array
     * @throws \Exception
     */
    public function getPurchasedTicketsForRecurringEvent($recurringEvent, $sectorId = null)
    {
        return $this->ticketResource->getPurchasedTickets($recurringEvent->getProductId(), $sectorId);
    }

    /**
     * Get available ticket qty for recurring event by sector
     *
     * @param int $sectorId
     * @param CartItemInterface $quoteItem
     *
     * @return int
     * @throws LocalizedException
     */
    public function getAvailableTicketQtyBySector($sectorId, $quoteItem)
    {
        $productId = $quoteItem->getProduct()->getId();
        $eventStartDate = $quoteItem->getProduct()->getCustomOption(OptionInterface::RECURRING_START_DATE);
        $timeSlot = $quoteItem->getProduct()->getCustomOption(OptionInterface::RECURRING_TIME_SLOT_ID);

        if (!$eventStartDate || !$timeSlot) {
            return 0;
        }

        $usedTickets = $this->ticketResource->getPurchasedTicketsQtyForEvent(
            $productId,
            $eventStartDate->getValue(),
            $timeSlot->getValue(),
            $sectorId
        );
        $sectorCapacity = $this->getDefaultTicketCount($sectorId);

        return ($sectorCapacity > $usedTickets) ? ($sectorCapacity - $usedTickets) : 0;
    }

    /**
     * Get ticket count available for sale by default
     *
     * @param string $sectorId
     * @return int
     */
    public function getDefaultTicketCount($sectorId)
    {
        try {
            $sector = $this->sectorRepository->get($sectorId);

            return $sector->getTicketsQty();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Resolve default tickets qty for each sector
     *
     * @param Sector[] $sectorConfig
     * @return array
     */
    public function resolveDefaultQtyForSectors($sectorConfig)
    {
        $result = [];
        foreach ($sectorConfig as $sector) {
            $sectorId = $sector->getSectorId();
            $result[$sectorId] = $this->getDefaultTicketCount($sectorId);
        }

        return $result;
    }
}
