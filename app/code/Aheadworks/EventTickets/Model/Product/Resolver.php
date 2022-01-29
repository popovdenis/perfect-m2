<?php
namespace Aheadworks\EventTickets\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Output as CatalogOutputHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Product
 */
class Resolver
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CatalogOutputHelper
     */
    private $catalogOutputHelper;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CatalogOutputHelper $catalogOutputHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CatalogOutputHelper $catalogOutputHelper
    ) {
        $this->productRepository = $productRepository;
        $this->catalogOutputHelper = $catalogOutputHelper;
    }

    /**
     * Retrieve product name, prepared for the frontend displaying
     *
     * @param string $productSku
     * @return string
     */
    public function getPreparedNameBySku($productSku)
    {
        try {
            /** @var Product|ProductInterface $product */
            $product = $this->productRepository->get($productSku);
            $preparedName = $this->catalogOutputHelper->productAttribute(
                $product,
                $product->getName(),
                ProductInterface::NAME
            );
        } catch (LocalizedException $exception) {
            $preparedName = '';
        }
        return $preparedName;
    }
}
