<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Option;

/**
 * Interface ConfigurationInterface
 * @package Aheadworks\EventTickets\Model\Product\Additional\Option
 */
interface ConfigurationInterface
{
    /**
     * Process item options
     *
     * @param array $optionsData
     * @return array
     */
    public function processOptions($optionsData);
}
