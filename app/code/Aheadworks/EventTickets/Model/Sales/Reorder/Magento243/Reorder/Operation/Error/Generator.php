<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\Error;

use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\Error as ReorderDataError;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\ErrorFactory as ReorderDataErrorFactory;
use Magento\Sales\Model\Order\Item as SalesOrderItem;

class Generator
{
    /**#@+
     * Error message codes
     */
    const ERROR_PRODUCT_NOT_FOUND = 'PRODUCT_NOT_FOUND';
    const ERROR_INSUFFICIENT_STOCK = 'INSUFFICIENT_STOCK';
    const ERROR_NOT_SALABLE = 'NOT_SALABLE';
    const ERROR_REORDER_NOT_AVAILABLE = 'REORDER_NOT_AVAILABLE';
    const ERROR_UNDEFINED = 'UNDEFINED';
    /**#@-*/

    /**
     * List of error messages and codes.
     */
    const MESSAGE_CODES = [
        'The required options you selected are not available' => self::ERROR_NOT_SALABLE,
        'Product that you are trying to add is not available' => self::ERROR_NOT_SALABLE,
        'This product is out of stock' => self::ERROR_NOT_SALABLE,
        'There are no source items' => self::ERROR_NOT_SALABLE,
        'The fewest you may purchase is' => self::ERROR_INSUFFICIENT_STOCK,
        'The most you may purchase is' => self::ERROR_INSUFFICIENT_STOCK,
        'The requested qty is not available' => self::ERROR_INSUFFICIENT_STOCK,
    ];

    /**
     * @var ReorderDataErrorFactory
     */
    private $reorderDataErrorFactory;

    /**
     * @param ReorderDataErrorFactory $reorderDataErrorFactory
     */
    public function __construct(
        ReorderDataErrorFactory $reorderDataErrorFactory
    ) {
        $this->reorderDataErrorFactory = $reorderDataErrorFactory;
    }

    /**
     * Create reorder error description object
     *
     * @param string $message
     * @param string|null $code
     * @return ReorderDataError
     */
    public function createByMessage(string $message, string $code = null)
    {
        /** @var ReorderDataError $reorderDataError */
        $reorderDataError = $this->reorderDataErrorFactory->create(
            [
                'message' => $message,
                'code' => $code ?? $this->getErrorCode($message)
            ]
        );

        return $reorderDataError;
    }

    /**
     * Create order line item error
     *
     * @param SalesOrderItem $salesOrderItem
     * @param Product $product
     * @param string|null $message
     * @param string|null $code
     * @return ReorderDataError
     */
    public function createBySalesOrderItem(
        SalesOrderItem $salesOrderItem,
        Product $product,
        string $message = null,
        string $code = null
    ) {
        $message = $this->getCartItemErrorMessage(
            $salesOrderItem,
            $product,
            $message
        );

        return $this->createByMessage($message, $code);
    }

    /**
     * Get message error code. Ad-hoc solution based on message parsing.
     *
     * @param string $message
     * @return string
     */
    protected function getErrorCode(string $message): string
    {
        $code = self::ERROR_UNDEFINED;

        $matchedCodes = array_filter(
            self::MESSAGE_CODES,
            function ($key) use ($message) {
                return false !== strpos($message, $key);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($matchedCodes)) {
            $code = current($matchedCodes);
        }

        return $code;
    }

    /**
     * Get error message for a cart item
     *
     * @param SalesOrderItem $salesOrderItem
     * @param Product $product
     * @param string|null $message
     * @return string
     */
    protected function getCartItemErrorMessage(
        SalesOrderItem $salesOrderItem,
        Product $product,
        string $message = null
    ) {
        // try to get sku from line-item first.
        // for complex product type: if custom option is not available it can cause error
        $sku = $salesOrderItem->getSku() ?? $product->getData('sku');
        return (string)($message
            ? __('Could not add the product with SKU "%1" to the shopping cart: %2', $sku, $message)
            : __('Could not add the product with SKU "%1" to the shopping cart', $sku));
    }
}
