<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Configurable
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product
 */
class Configurable implements ProductResolverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($item)
    {
        $product = $item->getProduct();
        if ($product->getExtensionAttributes()->getAwEtExclusiveProduct() === false) {
            try {
                $childProduct = $this->productRepository->get($item->getSku());
                $product = clone $product;
                $product->getExtensionAttributes()->setAwEtExclusiveProduct(
                    $childProduct->getExtensionAttributes()->getAwEtExclusiveProduct()
                );
            } catch (NoSuchEntityException $e) {
            }
        }
        return $product;
    }
}
