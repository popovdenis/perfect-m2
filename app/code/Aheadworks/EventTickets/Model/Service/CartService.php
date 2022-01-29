<?php
namespace Aheadworks\EventTickets\Model\Service;

use Aheadworks\EventTickets\Model\Cart\Cart;
use Aheadworks\EventTickets\Api\CartManagementInterface;
use Aheadworks\EventTickets\Model\Cart\Item\Factory as CartItemFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\EventTickets\Model\Cart\Area\Resolver as CartAreaResolver;

/**
 * Class CartService
 * @package Aheadworks\EventTickets\Model\Service
 */
class CartService implements CartManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartItemFactory
     */
    private $cartItemFactory;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var CartAreaResolver
     */
    private $cartAreaResolver;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param CartItemFactory $cartItemFactory
     * @param Cart $cart
     * @param CartAreaResolver $cartAreaResolver
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CartItemFactory $cartItemFactory,
        Cart $cart,
        CartAreaResolver $cartAreaResolver
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->cart = $cart;
        $this->cartAreaResolver = $cartAreaResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function addRelatedProducts($cartId, $additionalProductsOptions)
    {
        $newCartItems = [];
        foreach ($additionalProductsOptions as $additionalProductOptions) {
            if ($additionalProductOptions->getQty() > 0) {
                $newCartItems[] = $this->cartItemFactory->create($cartId, $additionalProductOptions);
            }
        }

        if ($newCartItems) {
            $quote = $this->cartAreaResolver->resolve($cartId);
            $this->cart->addProductsToCart($quote, $newCartItems);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnusedRelatedProducts($cart)
    {
        if ($cart instanceof Quote) {
            $quote = $cart;
        } else {
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($cart);
        }
        $updatedQuote = $this->cart->removeUnusedRelatedProducts($quote);
        if ($updatedQuote) {
            $this->cartRepository->save($updatedQuote);
        }
    }
}
