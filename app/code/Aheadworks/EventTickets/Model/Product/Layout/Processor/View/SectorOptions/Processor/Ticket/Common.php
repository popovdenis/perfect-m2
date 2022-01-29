<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Resolver;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Model\Product;

/**
 * Class Common
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket
 */
class Common implements TicketBuilderProcessorInterface
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(
        Resolver $resolver
    ) {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $ticket, $ticketRender)
    {
        $sectorId = $sector->getSectorId();
        $ticketTypeId = $ticket->getTypeId();
        /** @var EventTicket $productType */
        $productType = $product->getTypeInstance();
        $isAllPersonalOptionEmpty = $productType->isAllPersonalOptionEmpty($product);
        $ticketRender
            ->setQty($this->getQty($product, $sectorId, $ticketTypeId))
            ->setAvailableOptionUids($ticket->getPersonalOptionUids())
            ->setIsAllPersonalOptionEmpty($isAllPersonalOptionEmpty);

        return $ticketRender;
    }

    /**
     * Retrieve qty
     *
     * @param Product $product
     * @param int $sectorId
     * @param int $ticketTypeId
     * @return int
     */
    private function getQty($product, $sectorId, $ticketTypeId)
    {
        $preConfigSectorId = $this->resolver
            ->resolvePreconfiguredOptionValue($product, OptionInterface::BUY_REQUEST_SECTOR_ID);
        $preConfigTicketTypeId = $this->resolver
            ->resolvePreconfiguredOptionValue($product, OptionInterface::BUY_REQUEST_TYPE_ID);
        if ($preConfigSectorId == $sectorId && $preConfigTicketTypeId == $ticketTypeId) {
            return (int)$this->resolver->resolvePreconfiguredOptionValue($product, OptionInterface::BUY_REQUEST_QTY);
        }

        return 0;
    }
}
