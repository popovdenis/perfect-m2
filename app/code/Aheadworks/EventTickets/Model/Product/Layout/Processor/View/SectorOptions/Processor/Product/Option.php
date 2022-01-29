<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

// @codingStandardsIgnoreStart
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\ConfigurationPool;
// @codingStandardsIgnoreEnd
use Psr\Log\LoggerInterface;

/**
 * Class Option
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
class Option implements ProductBuilderProcessorInterface
{
    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ConfigurationPool $configurationPool
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigurationPool $configurationPool,
        LoggerInterface $logger
    ) {
        $this->configurationPool = $configurationPool;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function build($product, $productRender)
    {
        $configurationOptions = [];
        $productType = $product->getTypeId();
        if ($this->configurationPool->hasConfiguration($productType)) {
            try {
                $configuration = $this->configurationPool->getConfiguration($productType);
                $configurationOptions = $configuration->getOptions($product);
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }
        $serializedOptions = \Zend_Json::encode($configurationOptions);
        $productRender->setOption($serializedOptions);
    }
}
