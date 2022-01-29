<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Model\ProductRenderFactory;

/**
 * Class Price
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
class Price implements ProductBuilderProcessorInterface
{
    /**
     * @var ProductBuilderProcessorInterface[]
     */
    private $priceProviders = [];

    /**
     * @var ProductRenderFactory
     */
    private $productRenderFactory;

    /**
     * @param ProductRenderFactory $productRenderFactory
     * @param array $priceProviders
     */
    public function __construct(
        ProductRenderFactory $productRenderFactory,
        array $priceProviders = []
    ) {
        $this->priceProviders = $priceProviders;
        $this->productRenderFactory = $productRenderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $productRender)
    {
        /** @var ProductRenderInterface $catalogProductRender */
        $catalogProductRender = $this->productRenderFactory->create();
        $catalogProductRender->setStoreId($productRender->getStoreId());
        $catalogProductRender->setCurrencyCode($productRender->getCurrencyCode());

        foreach ($this->priceProviders as $provider) {
            $provider->collect($product, $catalogProductRender);
        }

        $productRender->setPriceInfo($catalogProductRender->getPriceInfo());
    }
}
