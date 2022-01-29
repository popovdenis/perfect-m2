<?php
namespace Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\TicketEntity;

use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SectorInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterfaceFactory;

/**
 * Class EmptyEntityCreator
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\TicketEntity
 */
class EmptyEntityCreator
{
    /**
     * @var VenueInterfaceFactory
     */
    private $venueDataFactory;

    /**
     * @var SpaceInterfaceFactory
     */
    private $spaceDataFactory;

    /**
     * @var SectorInterfaceFactory
     */
    private $sectorDataFactory;

    /**
     * @var TicketTypeInterfaceFactory
     */
    private $ticketTypeDataFactory;

    /**
     * @var StorefrontLabelsInterfaceFactory
     */
    private $storefrontLabelsFactory;

    /**
     * @param VenueInterfaceFactory $venueDataFactory
     * @param SpaceInterfaceFactory $spaceDataFactory
     * @param SectorInterfaceFactory $sectorDataFactory
     * @param TicketTypeInterfaceFactory $ticketTypeDataFactory
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     */
    public function __construct(
        VenueInterfaceFactory $venueDataFactory,
        SpaceInterfaceFactory $spaceDataFactory,
        SectorInterfaceFactory $sectorDataFactory,
        TicketTypeInterfaceFactory $ticketTypeDataFactory,
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
    ) {
        $this->venueDataFactory = $venueDataFactory;
        $this->spaceDataFactory = $spaceDataFactory;
        $this->sectorDataFactory = $sectorDataFactory;
        $this->ticketTypeDataFactory = $ticketTypeDataFactory;
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
    }

    /**
     * Create empty venue object
     *
     * @return VenueInterface
     */
    public function createVenue()
    {
        /** @var VenueInterface $venue */
        $venue = $this->venueDataFactory->create();
        return $this->setCurrentLabels($venue);
    }

    /**
     * Create empty space object
     *
     * @return SpaceInterface
     */
    public function createSpace()
    {
        /** @var SpaceInterface $space */
        $space = $this->spaceDataFactory->create();
        return $this->setCurrentLabels($space);
    }

    /**
     * Create empty ticket type object
     *
     * @return TicketTypeInterface
     */
    public function createTicketType()
    {
        /** @var TicketTypeInterface $ticketType */
        $ticketType = $this->ticketTypeDataFactory->create();
        return $this->setCurrentLabels($ticketType);
    }

    /**
     * Create empty sector object
     *
     * @return SectorInterface
     */
    public function createSector()
    {
        /** @var SectorInterface $sector */
        $sector = $this->sectorDataFactory->create();
        return $this->setCurrentLabels($sector);
    }

    /**
     * Set current labels for storefront labels entity
     *
     * @param StorefrontLabelsEntityInterface $storefrontLabelsEntity
     * @return StorefrontLabelsEntityInterface
     */
    private function setCurrentLabels($storefrontLabelsEntity)
    {
        /** @var StorefrontLabelsInterface $currentLabels */
        $currentLabels = $this->storefrontLabelsFactory->create();
        $storefrontLabelsEntity->setCurrentLabels($currentLabels);
        return $storefrontLabelsEntity;
    }
}
