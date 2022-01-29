<?php
// @codingStandardsIgnoreStart
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration;

use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable\Factory;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\ConfigurationInterface;

/**
 * Class Configurable
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration
 */
class Configurable implements ConfigurationInterface
{
    // @codingStandardsIgnoreEnd
    /**
     * @var Factory
     */
    private $configFactory;

    /**
     * @param Factory $configFactory
     */
    public function __construct(
        Factory $configFactory
    ) {
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions($product)
    {
        $config = $this->configFactory->create($product);
        $options = $config
            ? $config->getOptions()
            : [];

        return $options;
    }
}
