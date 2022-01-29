<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Price as CatalogPrice;
use Magento\CatalogRule\Model\ResourceModel\RuleFactory;

/**
 * Class Price
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket
 */
class Price extends CatalogPrice
{
    /**
     * {@inheritdoc}
     */
    public function getBasePrice($product, $qty = null)
    {
        return $this->applyAmounts($product, (float)$product->getPrice());
    }

    /**
     * Calculate final price of selection
     *
     * @param Product $product
     * @param int $sectorId
     * @param int $ticketTypeId
     * @return int|null
     */
    public function getSelectionFinalPrice($product, $sectorId, $ticketTypeId)
    {
        $sectorConfig = $product->getTypeInstance()->getSectorConfig($product);
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            foreach ($sector->getSectorTickets() as $ticket) {
                if ($sectorId == $sector->getSectorId() && $ticketTypeId == $ticket->getTypeId()) {
                    return $ticket->getFinalPrice();
                }
            }
        }

        return null;
    }

    /**
     * Retrieve product amounts
     *
     * @param Product $product
     * @return array
     */
    public function getAmounts(Product $product)
    {
        return $product->getTypeInstance()->getAmounts($product);
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice($product)
    {
        return (float)$product->getData('price');
    }

    /**
     * Apply Event Ticket amount for product
     *
     * @param Product $product
     * @param float $price
     * @return float
     */
    private function applyAmounts(Product $product, $price)
    {
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption(OptionInterface::AMOUNT);
            if ($customOption) {
                $price += $customOption->getValue();
            }
        }
        return $price;
    }
}
