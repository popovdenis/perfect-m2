<?php
namespace Aheadworks\EventTickets\Api;

/**
 * Interface GuestCartManagementInterface
 * @api
 */
interface GuestCartManagementInterface
{
    /**
     * Add related products to cart
     *
     * @param int $cartId
     * @param \Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface[] $additionalProductsOptions
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addRelatedProducts($cartId, $additionalProductsOptions);

    /**
     * Remove unused related exclusive products from cart
     *
     * @param int $cartId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeUnusedRelatedProducts($cartId);
}
