<?php
namespace Aheadworks\EventTickets\Model\Import\Processor\SectorConfig;

use Aheadworks\EventTickets\Model\Import\ArrayProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Product
 * @package Aheadworks\EventTickets\Model\Import\Processor\SectorConfig
 */
class Product
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
     * @var ArrayProcessor
     */
    private $arrayProcessor;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ArrayProcessor $arrayProcessor
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ArrayProcessor $arrayProcessor
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->arrayProcessor = $arrayProcessor;
    }

    /**
     * Change product sku to id
     *
     * @param array $array
     * @param string $fieldName
     * @return array
     */
    public function changeProductSkuToId($array, $fieldName = 'product_id')
    {
        $array = $this->arrayProcessor->removeEmptyValuesAndSubArrays($array);
        $productsSku = $this->extractProductsSku($array, $fieldName);

        $this->searchCriteriaBuilder->addFilter(ProductInterface::SKU, $productsSku, 'in');
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $processedArray = $this->changeSkuToId($array, $products, $fieldName);
        $processedArray = $this->arrayProcessor->removeEmptyValuesAndSubArrays($processedArray);

        return $processedArray;
    }

    /**
     * Change sku to id
     *
     * @param array $array
     * @param ProductInterface[] $products
     * @param string $fieldName
     * @return array
     */
    private function changeSkuToId($array, $products, $fieldName)
    {
        $processed = true;
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->changeSkuToId($value, $products, $fieldName);
            } elseif ($key == $fieldName) {
                if ($productId = $this->getProductIdBySku($value, $products)) {
                    $array[$key] = $productId;
                } else {
                    $processed = false;
                }
            }
        }
        return $processed ? $array : [];
    }

    /**
     * Retrieve product id by sku
     *
     * @param $sku
     * @param ProductInterface[] $products
     * @return null|int
     */
    private function getProductIdBySku($sku, $products)
    {
        foreach ($products as $product) {
            if ($product->getSku() == $sku) {
                return $product->getId();
            }
        }
        return null;
    }

    /**
     * Extract products sku
     *
     * @param array $array
     * @param string $fieldName
     * @return array
     */
    private function extractProductsSku($array, $fieldName)
    {
        $productsSku = [];
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $productsSku[] = $this->extractProductsSku($value, $fieldName);
            } elseif ($key == $fieldName) {
                $productsSku[] = $value;
            }
        }
        return $productsSku;
    }
}
