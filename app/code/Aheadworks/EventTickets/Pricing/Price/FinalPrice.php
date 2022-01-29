<?php
namespace Aheadworks\EventTickets\Pricing\Price;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Catalog\Pricing\Price\FinalPrice as CatalogFinalPrice;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Price as EventTicketProductPrice;

/**
 * Class FinalPrice
 *
 * @package Aheadworks\EventTickets\Pricing\Price
 */
class FinalPrice extends CatalogFinalPrice
{
    /**
     * {@inheritdoc}
     */
    public function getMaximalPrice()
    {
        if ($this->maximalPrice === null) {
            $price = 0;
            $amounts = $this->getPriceModel()->getAmounts($this->getProduct());
            if (!empty($amounts)) {
                $price = max($amounts);
            }
            $this->maximalPrice = $this->calculator->getAmount(
                $this->priceCurrency->convertAndRound($price),
                $this->getProduct()
            );
        }
        return $this->maximalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimalPrice()
    {
        if ($this->minimalPrice === null) {
            $price = 0;
            $amounts = $this->getPriceModel()->getAmounts($this->getProduct());
            if (!empty($amounts)) {
                $price = min($amounts);
            }
            $this->minimalPrice = $this->calculator->getAmount(
                $this->priceCurrency->convertAndRound($price),
                $this->getProduct()
            );
        }
        return $this->minimalPrice;
    }

    /**
     * Retrieve product price model
     *
     * @return EventTicketProductPrice
     */
    private function getPriceModel()
    {
        return $this->getProduct()->getPriceModel();
    }
}
