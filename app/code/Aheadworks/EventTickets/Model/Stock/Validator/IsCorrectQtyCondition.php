<?php
namespace Aheadworks\EventTickets\Model\Stock\Validator;

use Aheadworks\EventTickets\Api\Data\IsAvailableResultInterface;
use Aheadworks\EventTickets\Model\Stock\Item\Configuration\Resolver as StockItemConfigurationResolver;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Aheadworks\EventTickets\Api\Data\IsAvailableResultInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\IsAvailableErrorInterfaceFactory;
use Magento\Framework\Phrase;
use Aheadworks\EventTickets\Model\Product\Resolver as ProductResolver;

/**
 * Class IsCorrectQtyCondition
 *
 * @package Aheadworks\EventTickets\Model\Stock\Validator
 */
class IsCorrectQtyCondition
{
    /**
     * @var StockItemConfigurationResolver
     */
    private $stockItemConfigurationResolver;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var IsAvailableResultInterfaceFactory
     */
    private $availableResultInterfaceFactory;

    /**
     * @var IsAvailableErrorInterfaceFactory
     */
    private $availableErrorInterfaceFactory;

    /**
     * @param StockItemConfigurationResolver $stockItemResolver
     * @param ProductResolver $productResolver
     * @param IsAvailableResultInterfaceFactory $availableResultInterfaceFactory
     * @param IsAvailableErrorInterfaceFactory $availableErrorInterfaceFactory
     */
    public function __construct(
        StockItemConfigurationResolver $stockItemResolver,
        ProductResolver $productResolver,
        IsAvailableResultInterfaceFactory $availableResultInterfaceFactory,
        IsAvailableErrorInterfaceFactory $availableErrorInterfaceFactory
    ) {
        $this->stockItemConfigurationResolver = $stockItemResolver;
        $this->productResolver = $productResolver;
        $this->availableResultInterfaceFactory = $availableResultInterfaceFactory;
        $this->availableErrorInterfaceFactory = $availableErrorInterfaceFactory;
    }

    /**
     * Detects whether a certain qty of product is salable for a given website and its stock data
     *
     * @param float $requestedQty
     * @param string|null $productSku
     * @param int $websiteId
     * @return IsAvailableResultInterface
     */
    public function execute($requestedQty, $productSku, $websiteId)
    {
        if (empty($productSku)) {
            return $this->createErrorResult(
                'incorrect_sku',
                __('Some of the product are not available.')
            );
        }

        $stockItemConfiguration = $this->stockItemConfigurationResolver->getForProduct(
            $productSku,
            $websiteId
        );

        if ($stockItemConfiguration) {
            if ($this->isMinSaleQuantityCheckFailed($stockItemConfiguration, $requestedQty)) {
                $productName = $this->productResolver->getPreparedNameBySku($productSku);
                return $this->createErrorResult(
                    'is_correct_qty-min_sale_qty',
                    __(
                        'The fewest quantity of product %1 you may purchase is %2.',
                        $productName,
                        $stockItemConfiguration->getMinSaleQty()
                    )
                );
            }
            if ($this->isMaxSaleQuantityCheckFailed($stockItemConfiguration, $requestedQty)) {
                $productName = $this->productResolver->getPreparedNameBySku($productSku);
                return $this->createErrorResult(
                    'is_correct_qty-max_sale_qty',
                    __(
                        'The requested qty of product %1 exceeds the maximum qty allowed in shopping cart.',
                        $productName
                    )
                );
            }

            if ($this->isDecimalQtyCheckFailed($requestedQty)) {
                return $this->createErrorResult(
                    'is_correct_qty-is_qty_decimal',
                    __('You cannot use decimal quantity for this product.')
                );
            }
        }

        return $this->availableResultInterfaceFactory->create(['errors' => []]);
    }

    /**
     * Check if decimal quantity is valid
     *
     * @param int|float $requestedQty
     * @return bool
     */
    private function isDecimalQtyCheckFailed($requestedQty) {
        return (floor($requestedQty) !== (float)$requestedQty);
    }

    /**
     * Check if min sale condition is satisfied
     *
     * @param StockItemConfigurationInterface|StockItemInterface $stockItemConfiguration
     * @param float $requestedQty
     * @return bool
     */
    private function isMinSaleQuantityCheckFailed($stockItemConfiguration, float $requestedQty)
    {
        // Minimum Qty Allowed in Shopping Cart
        if ($stockItemConfiguration->getMinSaleQty()
            && $requestedQty < $stockItemConfiguration->getMinSaleQty()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check if max sale condition is satisfied
     *
     * @param StockItemConfigurationInterface|StockItemInterface $stockItemConfiguration
     * @param float $requestedQty
     * @return bool
     */
    private function isMaxSaleQuantityCheckFailed($stockItemConfiguration, $requestedQty)
    {
        // Maximum Qty Allowed in Shopping Cart
        if ($stockItemConfiguration->getMaxSaleQty()
            && $requestedQty > $stockItemConfiguration->getMaxSaleQty()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Create Error Result Object
     *
     * @param string $code
     * @param Phrase $message
     * @return IsAvailableResultInterface
     */
    private function createErrorResult($code, $message)
    {
        $errors = [
            $this->availableErrorInterfaceFactory->create([
                'code' => $code,
                'message' => $message
            ])
        ];
        return $this->availableResultInterfaceFactory->create(['errors' => $errors]);
    }
}
