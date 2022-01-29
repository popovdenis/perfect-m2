<?php
namespace Aheadworks\EventTickets\Model\Service;

use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Source\Product\Stock\Status;
use Aheadworks\EventTickets\Model\Stock\Resolver\TicketSellingDeadlineDate as TicketSellingDeadlineResolver;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\EventTickets\Model\Stock\Validator\IsCorrectQtyCondition as StockValidator;

/**
 * Class StockService
 *
 * @package Aheadworks\EventTickets\Model\Service
 */
class StockService implements StockManagementInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var TicketSellingDeadlineResolver
     */
    private $ticketSellingDeadlineResolver;

    /**
     * @var StockValidator
     */
    private $stockValidator;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param TimezoneInterface $localeDate
     * @param TicketSellingDeadlineResolver $ticketSellingDeadlineResolver
     * @param StockValidator $stockValidator
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        TimezoneInterface $localeDate,
        TicketSellingDeadlineResolver $ticketSellingDeadlineResolver,
        StockValidator $stockValidator
    ) {
        $this->productRepository = $productRepository;
        $this->localeDate = $localeDate;
        $this->ticketSellingDeadlineResolver = $ticketSellingDeadlineResolver;
        $this->stockValidator = $stockValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableTicketQty($productId)
    {
        $qty = 0;
        if ($product = $this->getProductById($productId)) {
            return $this->getAvailableProductTicketQty($product);
        }

        return $qty;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableProductTicketQty($product)
    {
        $qty = 0;
        if ($productType = $this->getProductType($product)) {
            $qty = $productType->getAvailableTicketQty($product);
        }

        return $qty;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableTicketQtyBySector($productId, $sectorId, $quoteItem = null)
    {
        $qty = 0;
        if ($product = $this->getProductById($productId)) {
            if ($productType = $this->getProductType($product)) {
                $qty = $productType->getAvailableTicketQtyBySector($product, $sectorId, $quoteItem);
            }
        }

        return $qty;
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketSectorStatus($productId, $sectorId)
    {
        $qty = $this->getAvailableTicketQtyBySector($productId, $sectorId);
        $isFree = $this->isFreeTicketsInSector($productId, $sectorId);
        $isTicketSellingDeadline = $this->isTicketSellingDeadline($productId);

        return $this->resolveStatus($qty, $isFree, $isTicketSellingDeadline);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketStatus($productId)
    {
        $qty = $this->getAvailableTicketQty($productId);
        $isFree = $this->isFreeTicketsByProductId($productId);
        $isTicketSellingDeadline = $this->isTicketSellingDeadline($productId);

        return $this->resolveStatus($qty, $isFree, $isTicketSellingDeadline);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTicketStatus($product)
    {
        $qty = $this->getAvailableProductTicketQty($product);
        $isFree = $this->isFreeTicketsByProduct($product);
        $isTicketSellingDeadline = $this->isProductTicketSellingDeadline($product);

        return $this->resolveStatus($qty, $isFree, $isTicketSellingDeadline);
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableTicketQtyBySector($qty, $productId, $sectorId, $quoteItem = null)
    {
        return $qty <= $this->getAvailableTicketQtyBySector($productId, $sectorId, $quoteItem);
    }

    /**
     * {@inheritdoc}
     */
    public function isTicketQtyAvailableByProduct($totalQtyOfProductTickets, $productId, $websiteId)
    {
        $product = $this->getProductById($productId);
        $productSku = ($product != false) ? $product->getSku() : null;
        $isAvailableResult = $this->stockValidator->execute(
            $totalQtyOfProductTickets,
            $productSku,
            $websiteId
        );
        return $isAvailableResult;
    }

    /**
     * {@inheritdoc}
     */
    public function isTicketSellingDeadline($productId, $quoteItem = null)
    {
        $deadline = true;
        if ($product = $this->getProductById($productId)) {
            return $this->isProductTicketSellingDeadline($product, $quoteItem);
        }

        return $deadline;
    }

    /**
     * {@inheritdoc}
     */
    public function isProductTicketSellingDeadline($product, $quoteItem = null)
    {
        $deadline = true;
        if ($this->getProductType($product)) {
            $deadlineDate = $this->ticketSellingDeadlineResolver->resolve($product, $quoteItem);
            $nowDate = $this->localeDate->scopeDate($product->getStore(), null, true);
            if ($nowDate < $deadlineDate) {
                $deadline = false;
            }
        }

        return $deadline;
    }

    /**
     * {@inheritdoc}
     */
    public function isSalable($productId)
    {
        $status = $this->getTicketStatus($productId);

        return $this->isSalableByStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function isProductSalable($product)
    {
        $status = $this->getProductTicketStatus($product);

        return $this->isSalableByStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function isSalableBySector($productId, $sectorId)
    {
        $status = $this->getTicketSectorStatus($productId, $sectorId);

        return $this->isSalableByStatus($status);
    }

    /**
     * Check if salable by status
     *
     * @param int $status
     * @return bool
     */
    private function isSalableByStatus($status)
    {
        return $status == Status::AVAILABLE || $status == Status::CAPACITY;
    }

    /**
     * Check if free tickets in sector
     *
     * @param int $productId
     * @param int $sectorId
     * @return bool
     */
    private function isFreeTicketsInSector($productId, $sectorId)
    {
        $isFree = false;
        if ($product = $this->getProductById($productId)) {
            if ($productType = $this->getProductType($product)) {
                $isFree = $productType->isFreeTicketsInSector($product, $sectorId);
            }
        }
        return $isFree;
    }

    /**
     * Check if free tickets by product id
     *
     * @param int $productId
     * @return bool
     */
    private function isFreeTicketsByProductId($productId)
    {
        $isFree = false;
        if ($product = $this->getProductById($productId)) {
            return $this->isFreeTicketsByProduct($product);
        }
        return $isFree;
    }

    /**
     * Check if free tickets by product
     *
     * @param Product $product
     * @return bool
     */
    private function isFreeTicketsByProduct($product)
    {
        $isFree = false;
        if ($productType = $this->getProductType($product)) {
            $isFree = $productType->isFreeTicketsByProduct($product);
        }
        return $isFree;
    }

    /**
     * Resolve status
     *
     * @param int $qty
     * @param bool $isFree
     * @param bool $isTicketSellingDeadline
     * @return int
     */
    private function resolveStatus($qty, $isFree, $isTicketSellingDeadline)
    {
        if ($isTicketSellingDeadline) {
            $status = Status::UNAVAILABLE;
        } elseif ($qty > 0) {
            if ($isFree) {
                $status = Status::CAPACITY;
            } else {
                $status = Status::AVAILABLE;
            }
        } else {
            if ($isFree) {
                $status = Status::FULL;
            } else {
                $status = Status::SOLD_OUT;
            }
        }

        return $status;
    }

    /**
     * Retrieve product by id
     *
     * @param int $productId
     * @return Product|ProductInterface|bool
     */
    private function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
        }
        return false;
    }

    /**
     * Retrieve product type
     *
     * @param Product $product
     * @return EventTicket|null
     */
    private function getProductType($product)
    {
        $typeInstance = $product->getTypeInstance();
        if ($typeInstance instanceof EventTicket) {
            return $typeInstance;
        }
        return null;
    }
}
