<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Option;

/**
 * Class ConfigurationPool
 * @package Aheadworks\EventTickets\Model\Product\Additional\Option
 */
class ConfigurationPool
{
    /**
     * @var array
     */
    private $configurations = [];

    /**
     * @param array $configurations
     */
    public function __construct(
        $configurations = []
    ) {
        $this->configurations = $configurations;
    }

    /**
     * Get configuration instance
     *
     * @param string $productType
     * @return ConfigurationInterface
     * @throws \Exception
     */
    public function getConfiguration($productType)
    {
        if (!isset($this->configurations[$productType])) {
            throw new \Exception(sprintf('Unknown configuration: %s requested', $productType));
        }
        $configurationInstance = $this->configurations[$productType];
        if (!$configurationInstance instanceof ConfigurationInterface) {
            throw new \Exception(
                sprintf('Configuration instance %s does not implement required interface.', $productType)
            );
        }

        return $configurationInstance;
    }

    /**
     * Check if configuration for product type exists
     *
     * @param string $productType
     * @return bool
     */
    public function hasConfiguration($productType)
    {
        return isset($this->configurations[$productType]);
    }
}
