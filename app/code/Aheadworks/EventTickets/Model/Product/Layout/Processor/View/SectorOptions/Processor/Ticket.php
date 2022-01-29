<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterfaceFactory;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket\TicketBuilder;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Resolver;
use Magento\Catalog\Model\Product;

/**
 * Class Ticket
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor
 */
class Ticket implements SectorBuilderProcessorInterface
{
    /**
     * @var TicketRenderInterfaceFactory
     */
    private $ticketRenderInterfaceFactory;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var TicketBuilder
     */
    private $ticketBuilder;

    /**
     * @param TicketRenderInterfaceFactory $ticketRenderInterfaceFactory
     * @param Resolver $resolver
     * @param TicketBuilder $ticketBuilder
     */
    public function __construct(
        TicketRenderInterfaceFactory $ticketRenderInterfaceFactory,
        Resolver $resolver,
        TicketBuilder $ticketBuilder
    ) {
        $this->ticketRenderInterfaceFactory = $ticketRenderInterfaceFactory;
        $this->resolver = $resolver;
        $this->ticketBuilder = $ticketBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $sectorRender)
    {
        $renderTickets = [];
        foreach ($sector->getSectorTickets() as $ticket) {
            if ($this->isSectorTicketAvailable($product, $sector->getSectorId(), $ticket->getTypeId())) {
                /** @var TicketRenderInterface $ticketRender */
                $ticketRender = $this->ticketRenderInterfaceFactory->create();
                $renderTickets[] = $this->ticketBuilder->build($product, $sector, $ticket, $ticketRender);
            }
        }
        $sectorRender
            ->setTickets($renderTickets);

        return $sectorRender;
    }

    /**
     * Check if sector available to display
     *
     * @param Product $product
     * @param int $sectorId
     * @param int $ticketTypeId
     * @return bool
     */
    private function isSectorTicketAvailable($product, $sectorId, $ticketTypeId)
    {
        $preConfigSectorId = $this->resolver
            ->resolvePreconfiguredOptionValue($product, OptionInterface::BUY_REQUEST_SECTOR_ID);
        $preConfigTicketTypeId = $this->resolver
            ->resolvePreconfiguredOptionValue($product, OptionInterface::BUY_REQUEST_TYPE_ID);
        $isConfigure = $this->resolver->isConfigureProduct($product);

        return !$isConfigure
            || ($isConfigure && $preConfigSectorId == $sectorId && $preConfigTicketTypeId == $ticketTypeId);
    }
}
