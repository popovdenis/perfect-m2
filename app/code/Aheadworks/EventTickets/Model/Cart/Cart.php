<?php
namespace Aheadworks\EventTickets\Model\Cart;

use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Cart
 * @package Aheadworks\EventTickets\Model\Cart
 */
class Cart
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Validator
     */
    private $exclusiveProductValidator;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param Validator $exclusiveProductValidator
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        Validator $exclusiveProductValidator
    ) {
        $this->cartRepository = $cartRepository;
        $this->exclusiveProductValidator = $exclusiveProductValidator;
    }

    /**
     * Add products to cart
     *
     * @param Quote $quote
     * @param CartItemInterface[] $newCartItems
     * @return CartInterface
     */
    public function addProductsToCart($quote, $newCartItems)
    {
        $quoteItems = $quote->getAllItems();
        $quoteItems = array_merge($quoteItems, $newCartItems);
        $quote->setItems($quoteItems);
        $this->cartRepository->save($quote);
        $quote->collectTotals();
        return $quote;
    }

    /**
     * Remove unused exclusive related products from cart
     *
     * @param Quote $quote
     * @return Quote|null
     */
    public function removeUnusedRelatedProducts($quote)
    {
        $updated = false;
        $items = $quote->getAllItems();
        /** @var Quote\Item $item */
        foreach ($items as $item) {
            try {
                if (!$item->getParentItemId()) {
                    $this->exclusiveProductValidator->validate($item, $quote, true);
                }
            } catch (LocalizedException $e) {
                $quote->removeItem($item->getId());
                $updated = true;
            }
        }
        return $updated ? $quote : null;
    }
}
