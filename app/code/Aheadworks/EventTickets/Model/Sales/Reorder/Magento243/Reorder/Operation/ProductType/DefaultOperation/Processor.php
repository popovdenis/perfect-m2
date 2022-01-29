<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType\DefaultOperation;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Framework\DataObject;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\Error\Generator
    as ReorderErrorGenerator;
use Magento\Sales\Model\Order\Item as SalesOrderItem;
use Psr\Log\LoggerInterface as Logger;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\Error as ReorderDataError;

class Processor
{
    /**
     * @var ReorderErrorGenerator
     */
    private $reorderErrorGenerator;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ReorderErrorGenerator $reorderErrorGenerator
     * @param Logger $logger
     */
    public function __construct(
        ReorderErrorGenerator $reorderErrorGenerator,
        Logger $logger
    ) {
        $this->reorderErrorGenerator = $reorderErrorGenerator;
        $this->logger = $logger;
    }

    /**
     * Safely add product to the cart
     *
     * @param Quote $cart
     * @param Product $product
     * @param DataObject $request
     * @param SalesOrderItem $orderItem
     * @return ReorderDataError[]
     */
    public function addProductToCart(
        Quote $cart,
        Product $product,
        DataObject $request,
        SalesOrderItem $orderItem
    ) {
        $addProductResult = null;
        $errorList = [];

        try {
            $addProductResult = $cart->addProduct($product, $request);
        } catch (LocalizedException $e) {
            $errorList[] = $this->reorderErrorGenerator->createBySalesOrderItem(
                $orderItem,
                $product,
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            $this->logger->critical($e);
            $errorList[] = $this->reorderErrorGenerator->createBySalesOrderItem(
                $orderItem,
                $product,
                null,
                ReorderErrorGenerator::ERROR_UNDEFINED
            );
        }

        // error happens in case the result is string
        if (is_string($addProductResult)) {
            $errorList = array_unique(explode("\n", $addProductResult));
            foreach ($errorList as $error) {
                $errorList[] = $this->reorderErrorGenerator->createBySalesOrderItem(
                    $orderItem,
                    $product,
                    $error
                );
            }
        }

        return $errorList;
    }
}
