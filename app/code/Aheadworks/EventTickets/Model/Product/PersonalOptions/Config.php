<?php
namespace Aheadworks\EventTickets\Model\Product\PersonalOptions;

use Aheadworks\EventTickets\Model\Product\PersonalOptions\Config\Reader;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data as ConfigData;

/**
 * Class Config
 *
 * @package Aheadworks\EventTickets\Model\Product\PersonalOptions
 */
class Config extends ConfigData
{
    /**
     * @param Reader $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        $cacheId = 'product_personal_options'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get configuration of all registered product personal options
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }

    /**
     * Retrieve types by group
     *
     * @param string $groupCode
     * @return array
     */
    public function getTypesByGroup($groupCode)
    {
        return array_keys($this->get($groupCode . '/types'));
    }
}
