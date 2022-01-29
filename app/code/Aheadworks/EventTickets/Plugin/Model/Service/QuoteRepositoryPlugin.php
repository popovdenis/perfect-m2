<?php
namespace Aheadworks\EventTickets\Plugin\Model\Service;

use Aheadworks\EventTickets\Api\CartManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class QuoteRepositoryPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Model\Service
 */
class QuoteRepositoryPlugin
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        CartManagementInterface $cartManagement
    ) {
        $this->cartManagement = $cartManagement;
    }

    /**
     * Remove unused promo products from cart
     *
     * @param QuoteRepository $subject
     * @param \Closure $proceed
     * @param CartInterface $quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function aroundSave($subject, \Closure $proceed, $quote)
    {
        $proceed($quote);
        $quoteId = $quote->getId();
        if ($quoteId) {
            try {
                $this->cartManagement->removeUnusedRelatedProducts($quoteId);
            } catch (LocalizedException $e) { } // phpcs:ignore
        }
    }
}
