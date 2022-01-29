<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProduct\ImageInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProduct\ImageInterfaceFactory;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class Image
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
class Image implements ProductBuilderProcessorInterface
{
    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * @var string
     */
    private $imageId;

    /**
     * @param ImageHelper $imageHelper
     * @param ImageInterfaceFactory $imageFactory
     * @param string $imageId
     */
    public function __construct(
        ImageHelper $imageHelper,
        ImageInterfaceFactory $imageFactory,
        $imageId = 'product_page_image_small'
    ) {
        $this->imageHelper = $imageHelper;
        $this->imageFactory = $imageFactory;
        $this->imageId = $imageId;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $productRender)
    {
        $imageHelper = $this->imageHelper->init($product, $this->imageId);
        /** @var ImageInterface $imageRender */
        $imageRender = $this->imageFactory->create();
        $imageRender
            ->setSrc($imageHelper->getUrl())
            ->setAlt($imageHelper->getLabel())
            ->setWidth($imageHelper->getWidth())
            ->setHeight($imageHelper->getHeight());

        $productRender->setImage($imageRender);
    }
}
