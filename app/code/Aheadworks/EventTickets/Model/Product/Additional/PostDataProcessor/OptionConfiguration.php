<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\PostDataProcessor;

use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface;
use Aheadworks\EventTickets\Model\PostDataProcessorInterface;
use Aheadworks\EventTickets\Model\Product\Additional\Option\ConfigurationPool;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Quote\Api\Data\ProductOptionInterface;

/**
 * Class OptionConfiguration
 * @package Aheadworks\EventTickets\Model\Product\Additional\PostDataProcessor
 */
class OptionConfiguration implements PostDataProcessorInterface
{
    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ConfigurationPool $configurationPool
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigurationPool $configurationPool,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    ) {
        $this->configurationPool = $configurationPool;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareEntityData($data)
    {
        try {
            $product = $this->productRepository->get($data[AdditionalProductOptionsInterface::SKU]);
            $productType = $product->getTypeId();
            if ($this->configurationPool->hasConfiguration($productType)) {
                $optionConfiguration = $this->configurationPool
                    ->getConfiguration($productType)
                    ->processOptions($data);
                if ($optionConfiguration) {
                    $data = array_merge(
                        $data,
                        [
                            AdditionalProductOptionsInterface::OPTION => [
                                ProductOptionInterface::EXTENSION_ATTRIBUTES_KEY => $optionConfiguration
                            ]
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $data;
    }
}
