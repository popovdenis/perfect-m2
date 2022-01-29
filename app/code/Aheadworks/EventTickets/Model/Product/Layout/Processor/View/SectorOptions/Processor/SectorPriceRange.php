<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\TicketRenderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class SectorPriceRange
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor
 */
class SectorPriceRange implements SectorBuilderProcessorInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $sectorRender)
    {
        $sectorRender
            ->setPriceRange($this->getTicketsPriceRange($sectorRender->getTickets(), $product->getStoreId()));

        return $sectorRender;
    }

    /**
     * Retrieve tickets price range
     *
     * @param TicketRenderInterface[]|null $tickets
     * @param int $storeId
     * @return string
     */
    private function getTicketsPriceRange($tickets, $storeId)
    {
        $priceAmounts = [];
        $tickets = is_array($tickets) ? $tickets : [];
        foreach ($tickets as $ticket) {
            $priceAmounts[] = $ticket->getPriceInfo()->getFinalPrice();
        }
        $min = min($priceAmounts);
        $max = max($priceAmounts);
        if ($min !== false && $max !== false && $min != $max) {
            $range = implode(
                ' - ',
                [$this->priceCurrency->format($min, $storeId), $this->priceCurrency->format($max, $storeId)]
            );
        } else {
            $price = $min !== false ? $min : ($max !== false ? $max : 0);
            $range = $this->priceCurrency->format($price, $storeId);
        }

        return $range;
    }
}
