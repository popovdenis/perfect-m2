<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data;

use Magento\Quote\Api\Data\CartInterface;

class ReorderOutput
{
    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @var Error[]
     */
    private $errors;

    /**
     * @param CartInterface $cart
     * @param Error[] $errors
     */
    public function __construct(CartInterface $cart, array $errors)
    {
        $this->cart = $cart;
        $this->errors = $errors;
    }

    /**
     * Get Shopping Cart
     *
     * @return CartInterface
     */
    public function getCart(): CartInterface
    {
        return $this->cart;
    }

    /**
     * Get errors happened during reorder
     *
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
