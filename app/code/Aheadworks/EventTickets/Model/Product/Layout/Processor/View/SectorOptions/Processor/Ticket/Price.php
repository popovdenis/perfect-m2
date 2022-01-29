<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket;

use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Price
    as ProductPriceComposite;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Model\ProductRenderFactory;

/**
 * Class Price
 *
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Ticket
 */
class Price implements TicketBuilderProcessorInterface
{
    /**
     * @var ProductPriceComposite
     */
    private $productPriceComposite;

    /**
     * @var ProductRenderFactory
     */
    private $productRenderFactory;

    /**
     * @param ProductPriceComposite $productPriceComposite
     * @param ProductRenderFactory $productRenderFactory
     */
    public function __construct(
        ProductPriceComposite $productPriceComposite,
        ProductRenderFactory $productRenderFactory
    ) {
        $this->productPriceComposite = $productPriceComposite;
        $this->productRenderFactory = $productRenderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $ticket, $ticketRender)
    {
        $newProduct = clone $product;
        $newProduct
            ->setQty(1)
            ->setPrice($ticket->getFinalPrice());

        /** @var ProductRenderInterface $catalogProductRender */
        $catalogProductRender = $this->productRenderFactory->create();
        $this->productPriceComposite->build($newProduct, $catalogProductRender);

        $ticketRender->setPriceInfo($catalogProductRender->getPriceInfo());

        return $ticketRender;
    }
}
