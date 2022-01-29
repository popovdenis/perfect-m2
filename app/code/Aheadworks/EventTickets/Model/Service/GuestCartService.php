<?php
namespace Aheadworks\EventTickets\Model\Service;

use Aheadworks\EventTickets\Api\CartManagementInterface;
use Aheadworks\EventTickets\Api\GuestCartManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestCartService
 * @package Aheadworks\EventTickets\Model\Service
 */
class GuestCartService implements GuestCartManagementInterface
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param CartManagementInterface $cartManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->cartManagement = $cartManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addRelatedProducts($cartId, $additionalProductsOptions)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $this->cartManagement->addRelatedProducts($quoteIdMask->getQuoteId(), $additionalProductsOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnusedRelatedProducts($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $this->cartManagement->removeUnusedRelatedProducts($quoteIdMask->getQuoteId());
    }
}
