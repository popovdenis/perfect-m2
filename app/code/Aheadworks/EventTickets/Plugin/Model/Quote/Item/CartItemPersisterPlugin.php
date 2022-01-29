<?php
namespace Aheadworks\EventTickets\Plugin\Model\Quote\Item;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use Magento\Quote\Model\Quote\Item\CartItemPersister;

/**
 * Class CartItemPersisterPlugin
 * @package Aheadworks\EventTickets\Plugin\Model\Quote\Item
 */
class CartItemPersisterPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartItemOptionsProcessor
     */
    private $cartItemOptionProcessor;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CartItemOptionsProcessor $cartItemOptionProcessor
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CartItemOptionsProcessor $cartItemOptionProcessor
    ) {
        $this->productRepository = $productRepository;
        $this->cartItemOptionProcessor = $cartItemOptionProcessor;
    }

    /**
     * Magento can not add multiple products to the cart,
     * because several products have id = null
     *
     * @param CartItemPersister $subject
     * @param \Closure $proceed
     * @param CartInterface $quote
     * @param CartItemInterface $item
     * @return CartItemInterface
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundSave(
        CartItemPersister $subject,
        \Closure $proceed,
        CartInterface $quote,
        CartItemInterface $item
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $qty = $item->getQty();
        if (!is_numeric($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        $cartId = $item->getQuoteId();
        $itemId = $item->getItemId();
        try {
            /** Update existing item */
            if (isset($itemId)) {
                $currentItem = $quote->getItemById($itemId);
                if (!$currentItem) {
                    throw new NoSuchEntityException(
                        __('Cart %1 does not contain item %2', $cartId, $itemId)
                    );
                }
                $productType = $currentItem->getProduct()->getTypeId();
                $buyRequestData = $this->cartItemOptionProcessor->getBuyRequest($productType, $item);
                if (is_object($buyRequestData)) {
                    /** Update item product options */
                    $item = $quote->updateItem($itemId, $buyRequestData);
                } else {
                    if ($item->getQty() !== $currentItem->getQty()) {
                        $currentItem->setQty($qty);
                        /**
                         * Qty validation errors are stored as items message
                         * @see \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator::validate
                         */
                        if (!empty($currentItem->getMessage())) {
                            throw new LocalizedException(__($currentItem->getMessage()));
                        }
                    }
                }
            } else {
                /** add new item to shopping cart */
                // custom code start
                $product = $this->productRepository->get($item->getSku(), false, null, true);
                // custom code end
                $productType = $product->getTypeId();
                $item = $quote->addProduct(
                    $product,
                    $this->cartItemOptionProcessor->getBuyRequest($productType, $item)
                );
                if (is_string($item)) {
                    throw new LocalizedException(__($item));
                }
            }
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save quote'));
        }
        // custom code start
        $itemId = $item->getId() ? : $item->getAwUniqueId();
        // custom code end
        foreach ($quote->getAllItems() as $quoteItem) {
            // custom code start
            /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
            $quoteItemId = $quoteItem->getId() ? : $quoteItem->getAwUniqueId();
            // custom code end
            if ($itemId == $quoteItemId) {
                $item = $this->cartItemOptionProcessor->addProductOptions($productType, $quoteItem);
                return $this->cartItemOptionProcessor->applyCustomOptions($item);
            }
        }
        throw new CouldNotSaveException(__('Could not save quote'));
    }
}
