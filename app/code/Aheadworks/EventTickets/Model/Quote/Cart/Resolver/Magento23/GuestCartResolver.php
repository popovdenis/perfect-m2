<?php
namespace Aheadworks\EventTickets\Model\Quote\Cart\Resolver\Magento23;

use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResourceModel;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class GuestCartResolver
{
    /**
     * @var GuestCartManagementInterface
     */
    private $guestCartManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var QuoteIdMaskResourceModel
     */
    private $quoteIdMaskResourceModel;

    /**
     * @var GuestCartRepositoryInterface
     */
    private $guestCartRepository;

    /**
     * @param GuestCartManagementInterface $guestCartManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteIdMaskResourceModel $quoteIdMaskResourceModel
     * @param GuestCartRepositoryInterface $guestCartRepository
     */
    public function __construct(
        GuestCartManagementInterface $guestCartManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteIdMaskResourceModel $quoteIdMaskResourceModel,
        GuestCartRepositoryInterface $guestCartRepository
    ) {
        $this->guestCartManagement = $guestCartManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteIdMaskResourceModel = $quoteIdMaskResourceModel;
        $this->guestCartRepository = $guestCartRepository;
    }

    /**
     * Create empty cart for guest
     *
     * @param string|null $predefinedMaskedQuoteId
     * @return Quote
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resolve(string $predefinedMaskedQuoteId = null): Quote
    {
        $maskedQuoteId = $this->guestCartManagement->createEmptyCart();

        if ($predefinedMaskedQuoteId !== null) {
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $this->quoteIdMaskResourceModel->load($quoteIdMask, $maskedQuoteId, 'masked_id');

            $quoteIdMask->setMaskedId($predefinedMaskedQuoteId);
            $this->quoteIdMaskResourceModel->save($quoteIdMask);
            $maskedQuoteId = $predefinedMaskedQuoteId;
        }

        return $this->guestCartRepository->get($maskedQuoteId);
    }
}
