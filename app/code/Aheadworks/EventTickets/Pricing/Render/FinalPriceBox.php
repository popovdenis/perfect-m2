<?php
namespace Aheadworks\EventTickets\Pricing\Render;

use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * Class FinalPriceBox
 *
 * @method bool getUseLinkForAsLowAs()
 * @method bool getDisplayMinimalPrice()
 * @package Aheadworks\EventTickets\Pricing\Render
 */
class FinalPriceBox extends BasePriceBox
{
    /**
     * Retrieve minimal price
     *
     * @return AmountInterface
     */
    public function getMinimalPrice()
    {
        return $this->getPrice()->getMinimalPrice();
    }

    /**
     * Retrieve maximal price
     *
     * @return AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->getPrice()->getMaximalPrice();
    }

    /**
     * Retrieve amount
     *
     * @return AmountInterface
     */
    public function getAmount()
    {
        return $this->getMinimalPrice() ? $this->getMinimalPrice() : $this->getMaximalPrice();
    }

    /**
     * Check is render from-to
     *
     * @return bool
     */
    public function isRenderFromTo()
    {
        return ($this->getMinimalPrice() && $this->getMaximalPrice()
            && $this->getMinimalPrice()->getValue() != $this->getMaximalPrice()->getValue());
    }

    /**
     * Check is render single
     *
     * @return bool
     */
    public function isRenderSingle()
    {
        return (bool)$this->getAmount();
    }
}
