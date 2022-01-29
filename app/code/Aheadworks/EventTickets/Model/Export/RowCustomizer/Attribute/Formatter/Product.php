<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Product
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class Product implements FormatterInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        try {
            $formattedValue = $this->productRepository->getById($value)->getSku();
        } catch (NoSuchEntityException $exception) {
            $formattedValue = '';
        }
        return $formattedValue;
    }
}
