<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket;

use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterface;
use Magento\Catalog\Model\Product;

/**
 * Class TicketBuilderProcessorInterface
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket
 */
interface TicketBuilderProcessorInterface
{
    /**
     * Build ticket render object
     *
     * @param Product $product
     * @param ProductSectorInterface $sector
     * @param ProductSectorTicketInterface $ticket
     * @param TicketRenderInterface $ticketRender
     * @return TicketRenderInterface
     */
    public function build($product, $sector, $ticket, $ticketRender);
}
