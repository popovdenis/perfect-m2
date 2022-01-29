<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderInterfaceFactory;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\ProductBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Validator;

/**
 * Class Product
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor
 */
class Product implements SectorBuilderProcessorInterface
{
    /**
     * @var AdditionalProductRenderInterfaceFactory
     */
    private $additionalProductRenderFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductBuilder
     */
    private $productBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param AdditionalProductRenderInterfaceFactory $additionalProductRenderFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductBuilder $productBuilder
     * @param StoreManagerInterface $storeManager
     * @param Validator $validator
     */
    public function __construct(
        AdditionalProductRenderInterfaceFactory $additionalProductRenderFactory,
        ProductRepositoryInterface $productRepository,
        ProductBuilder $productBuilder,
        StoreManagerInterface $storeManager,
        Validator $validator
    ) {
        $this->additionalProductRenderFactory = $additionalProductRenderFactory;
        $this->productRepository = $productRepository;
        $this->productBuilder = $productBuilder;
        $this->storeManager = $storeManager;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $sector, $sectorRender)
    {
        $additionalProducts = [];
        $store = $this->storeManager->getStore($product->getStoreId());
        foreach ($sector->getSectorProducts() as $sectorProduct) {
            $additionalProduct = $this->getProduct($sectorProduct->getProductId());
            if ($additionalProduct && $this->validator->isValid($additionalProduct)) {
                /** @var AdditionalProductRenderInterface $additionalProductRender */
                $additionalProductRender = $this->additionalProductRenderFactory->create();
                $additionalProductRender
                    ->setStoreId($store->getId())
                    ->setCurrencyCode($store->getCurrentCurrencyCode());

                $this->productBuilder->build($additionalProduct, $additionalProductRender);
                $additionalProducts[] = $additionalProductRender;
            }
        }
        $sectorRender->setAdditionalProducts($additionalProducts);

        return $sectorRender;
    }

    /**
     * Retrieve product by id
     *
     * @param int $productId
     * @return ProductInterface|null
     */
    private function getProduct($productId)
    {
        try {
            /** @var ProductInterface $product */
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }
        return $product;
    }
}
