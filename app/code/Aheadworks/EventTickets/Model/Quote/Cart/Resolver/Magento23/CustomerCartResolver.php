<?php
namespace Aheadworks\EventTickets\Model\Quote\Cart\Resolver\Magento23;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResourceModel;

class CustomerCartResolver
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
     * @var QuoteIdMaskResourceModel
     */
    private $quoteIdMaskResourceModel;

    /**
     * @var QuoteIdToMaskedQuoteIdInterface
     */
    private $quoteIdToMaskedQuoteId;

    /**
     * @param CartManagementInterface $cartManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteIdMaskResourceModel $quoteIdMaskResourceModel
     * @param QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteIdMaskResourceModel $quoteIdMaskResourceModel,
        QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
    ) {
        $this->cartManagement = $cartManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteIdMaskResourceModel = $quoteIdMaskResourceModel;
        $this->quoteIdToMaskedQuoteId = $quoteIdToMaskedQuoteId;
    }

    /**
     * Get customer cart by customer id with predefined masked quote id
     *
     * @param int $customerId
     * @param string|null $predefinedMaskedQuoteId
     * @return Quote
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function resolve(int $customerId, string $predefinedMaskedQuoteId = null): Quote
    {
        try {
            /** @var Quote $cart */
            $cart = $this->cartManagement->getCartForCustomer($customerId);
        } catch (NoSuchEntityException $e) {
            $this->cartManagement->createEmptyCartForCustomer($customerId);
            $cart = $this->cartManagement->getCartForCustomer($customerId);
        }
        try {
            $this->ensureQuoteMaskIdExist((int)$cart->getId(), $predefinedMaskedQuoteId);
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (AlreadyExistsException $e) {
            // do nothing, we already have masked id
        }

        return $cart;
    }

    /**
     * Create masked id for customer's active quote if it's not exists
     *
     * @param int $quoteId
     * @param string|null $predefinedMaskedQuoteId
     * @return void
     * @throws AlreadyExistsException
     */
    private function ensureQuoteMaskIdExist(int $quoteId, string $predefinedMaskedQuoteId = null): void
    {
        try {
            $maskedId = $this->quoteIdToMaskedQuoteId->execute($quoteId);
        } catch (NoSuchEntityException $e) {
            $maskedId = '';
        }
        if ($maskedId === '') {
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $quoteIdMask->setQuoteId($quoteId);
            if (null !== $predefinedMaskedQuoteId) {
                $quoteIdMask->setMaskedId($predefinedMaskedQuoteId);
            }
            $this->quoteIdMaskResourceModel->save($quoteIdMask);
        }
    }
}
