<?php
namespace Aheadworks\EventTickets\Plugin\Model\InventorySales;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class IsProductSalableConditionPlugin
 * @package Aheadworks\EventTickets\Plugin\Model\InventorySales
 */
class IsProductSalableConditionPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory
     */
    private $productSalableResultFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ObjectManagerInterface $objectManager
    ) {
        $this->productRepository = $productRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * Return empty salable result for event ticket product
     *
     * @param \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface $subject
     * @param callable $proceed
     * @param string $sku
     * @param int $stockId
     * @param float $requestedQty
     * @return \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface $subject,
        callable $proceed,
        string $sku,
        int $stockId,
        float $requestedQty
    ) {
        $product = $this->productRepository->get($sku);
        if ($product->getTypeId() == EventTicket::TYPE_CODE) {
            return $this->getProductSalableResultFactory()->create(['errors' => []]);
        }
        return $proceed($sku, $stockId, $requestedQty);
    }

    /**
     * Retrieve Inventory Product Salable Result Factory instance
     *
     * @return \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory
     */
    private function getProductSalableResultFactory()
    {
        if (null === $this->productSalableResultFactory) {
            $this->productSalableResultFactory = $this->objectManager->create(
                \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory::class
            );
        }
        return $this->productSalableResultFactory;
    }
}
