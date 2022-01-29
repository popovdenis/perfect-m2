<?php
namespace Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\EmailVariables;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Image
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor
 */
class Image
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @param ImageHelper $imageHelper
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ImageHelper $imageHelper,
        ProductRepositoryInterface $productRepository
    ) {
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var TicketInterface $ticket */
        $ticket = $variables[EmailVariables::TICKET];
        $variables[EmailVariables::EVENT_IMAGE_URL] = empty($ticket->getEventImage())
            ? null
            : $this->extractImageFromProduct($ticket);

        return $variables;
    }

    /**
     * Extract image from product
     *
     * @param TicketInterface $ticket
     * @return null|string
     */
    private function extractImageFromProduct($ticket)
    {
        try {
            $product = $this->productRepository->getById($ticket->getProductId());
            $image = $this->getMediumImageUrl($product, $ticket->getEventImage());
        } catch (NoSuchEntityException $e) {
            $image = null;
        }
        return $image;
    }

    /**
     * Retrieve medium image url
     *
     * @param ProductInterface|Product $product
     * @param string $imageFile
     * @return string
     */
    private function getMediumImageUrl($product, $imageFile)
    {
        return $this->imageHelper->init($product, 'product_page_image_medium_no_frame')
                ->setImageFile($imageFile)
                ->getUrl();
    }
}
