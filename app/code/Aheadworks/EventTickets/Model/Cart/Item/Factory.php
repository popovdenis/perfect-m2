<?php
namespace Aheadworks\EventTickets\Model\Cart\Item;

use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Api\Data\CartItemExtensionInterfaceFactory;
use Magento\Quote\Api\Data\CartItemExtensionInterface;

/**
 * Class Factory
 * @package Aheadworks\EventTickets\Model\Cart\Item
 */
class Factory
{
    /**
     * @var CartItemInterfaceFactory
     */
    private $cartItemFactory;

    /**
     * @var CartItemExtensionInterfaceFactory
     */
    private $cartItemExtensionFactory;

    /**
     * @param CartItemInterfaceFactory $cartItemFactory
     * @param CartItemExtensionInterfaceFactory $cartItemExtensionFactory
     */
    public function __construct(
        CartItemInterfaceFactory $cartItemFactory,
        CartItemExtensionInterfaceFactory $cartItemExtensionFactory
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->cartItemExtensionFactory = $cartItemExtensionFactory;
    }

    /**
     * @param int $cartId
     * @param AdditionalProductOptionsInterface $additionalProductOptions
     * @return CartItemInterface
     */
    public function create($cartId, $additionalProductOptions)
    {
        /** @var CartItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->create();
        $cartItem
            ->setQuoteId($cartId)
            ->setSku($additionalProductOptions->getSku())
            ->setQty($additionalProductOptions->getQty());

        if ($additionalProductOptions->getOption()) {
            $cartItem->setProductOption($additionalProductOptions->getOption());
        }

        $extensionAttributes = $this
            ->getExtensionAttributes($cartItem)
            ->setAwUniqueId(uniqid());
        $cartItem->setExtensionAttributes($extensionAttributes);

        return $cartItem;
    }

    /**
     * Retrieve cart item extension interface
     *
     * @param CartItemInterface $item
     * @return CartItemExtensionInterface
     */
    private function getExtensionAttributes($item)
    {
        $extensionAttributes = $item->getExtensionAttributes()
            ? $item->getExtensionAttributes()
            : $this->cartItemExtensionFactory->create();

        return $extensionAttributes;
    }
}
