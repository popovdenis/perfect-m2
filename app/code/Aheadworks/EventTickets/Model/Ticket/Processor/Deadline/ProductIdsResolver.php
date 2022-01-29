<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor\Deadline;

use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class ProductIdsResolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor\Deadline
 */
class ProductIdsResolver
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StockManagementInterface $stockManagement
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StockManagementInterface $stockManagement
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockManagement = $stockManagement;
    }

    /**
     * Retrieve product ids for which the deadline has arrived
     *
     * @return array
     */
    public function resolve()
    {
        $deadlineProductIds = [];
        $products = $this->getProductsToProcess();
        foreach ($products as $product) {
            $productId = $product->getId();
            if (!$this->stockManagement->isSalable($productId)) {
                $deadlineProductIds[] = $productId;
            }
        }

        return $deadlineProductIds;
    }

    /**
     * Retrieve products to process
     *
     * @return ProductInterface[]
     */
    private function getProductsToProcess()
    {
        $this->searchCriteriaBuilder
            ->addFilter(ProductInterface::TYPE_ID, EventTicket::TYPE_CODE)
            ->addFilter(ProductInterface::STATUS, Status::STATUS_ENABLED);

        return $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
