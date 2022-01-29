<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket;

/**
 * Class TicketBuilder
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket
 */
class TicketBuilder
{
    /**
     * @var TicketBuilderProcessorInterface[]
     */
    private $processors;

    /**
     * @param TicketBuilderProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $ticket, $ticketRender)
    {
        foreach ($this->processors as $processor) {
            $processor->build($product, $sector, $ticket, $ticketRender);
        }
        return $ticketRender;
    }
}
