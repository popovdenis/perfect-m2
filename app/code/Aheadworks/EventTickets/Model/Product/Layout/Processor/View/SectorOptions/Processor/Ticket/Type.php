<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\Ticket\TypeInterfaceFactory;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Resolver;

/**
 * Class Type
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket
 */
class Type implements TicketBuilderProcessorInterface
{
    /**
     * @var TypeInterfaceFactory
     */
    private $ticketTypeRenderFactory;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @param TypeInterfaceFactory $ticketTypeRenderFactory
     * @param Resolver $resolver
     */
    public function __construct(TypeInterfaceFactory $ticketTypeRenderFactory, Resolver $resolver)
    {
        $this->ticketTypeRenderFactory = $ticketTypeRenderFactory;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $ticket, $ticketRender)
    {
        $ticketTypeId = $ticket->getTypeId();
        /** @var TypeInterface $ticketTypeRender */
        $ticketTypeRender = $this->ticketTypeRenderFactory->create();
        $ticketTypeRender
            ->setId($ticketTypeId)
            ->setDescription($this->resolver->resolveTicketTypeDescription($ticketTypeId))
            ->setLabel($this->resolver->resolveTicketTypeLabel($ticketTypeId));

        $ticketRender
            ->setTicketType($ticketTypeRender);

        return $ticketRender;
    }
}
