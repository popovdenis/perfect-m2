<?php
namespace Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver;

use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\TicketEntity\EmptyEntityCreator;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class TicketEntity
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver
 */
class TicketEntity
{
    /**
     * @var VenueRepositoryInterface
     */
    private $venueRepository;

    /**
     * @var SpaceRepositoryInterface
     */
    private $spaceRepository;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var EmptyEntityCreator
     */
    private $emptyEntityCreator;

    /**
     * @param VenueRepositoryInterface $venueRepository
     * @param SpaceRepositoryInterface $spaceRepository
     * @param SectorRepositoryInterface $sectorRepository
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param EmptyEntityCreator $emptyEntityCreator
     */
    public function __construct(
        VenueRepositoryInterface $venueRepository,
        SpaceRepositoryInterface $spaceRepository,
        SectorRepositoryInterface $sectorRepository,
        TicketTypeRepositoryInterface $ticketTypeRepository,
        EmptyEntityCreator $emptyEntityCreator
    ) {
        $this->venueRepository = $venueRepository;
        $this->spaceRepository = $spaceRepository;
        $this->sectorRepository = $sectorRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->emptyEntityCreator = $emptyEntityCreator;
    }

    /**
     * Resolve venue
     *
     * @param int $venueId
     * @param int $storeId
     * @return \Aheadworks\EventTickets\Api\Data\VenueInterface
     */
    public function resolveVenue($venueId, $storeId)
    {
        try {
            $venue = $this->venueRepository->get($venueId, $storeId);
        } catch (NoSuchEntityException $e) {
            $venue = $this->emptyEntityCreator->createVenue();
        }

        return $venue;
    }

    /**
     * Resolve space
     *
     * @param int $spaceId
     * @param int $storeId
     * @return \Aheadworks\EventTickets\Api\Data\SpaceInterface
     */
    public function resolveSpace($spaceId, $storeId)
    {
        try {
            $space = $this->spaceRepository->get($spaceId, $storeId);
        } catch (NoSuchEntityException $e) {
            $space = $this->emptyEntityCreator->createSpace();
        }

        return $space;
    }

    /**
     * Resolve ticket type
     *
     * @param int $ticketTypeId
     * @param int $storeId
     * @return \Aheadworks\EventTickets\Api\Data\TicketTypeInterface
     */
    public function resolveTicketType($ticketTypeId, $storeId)
    {
        try {
            $ticketType = $this->ticketTypeRepository->get($ticketTypeId, $storeId);
        } catch (NoSuchEntityException $e) {
            $ticketType = $this->emptyEntityCreator->createTicketType();
        }

        return $ticketType;
    }

    /**
     * Resolve sector id
     *
     * @param int $sectorId
     * @param int $storeId
     * @return \Aheadworks\EventTickets\Api\Data\SectorInterface
     */
    public function resolveSector($sectorId, $storeId)
    {
        try {
            $sector = $this->sectorRepository->get($sectorId, $storeId);
        } catch (NoSuchEntityException $e) {
            $sector = $this->emptyEntityCreator->createSector();
        }

        return $sector;
    }
}
