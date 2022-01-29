<?php
// @codingStandardsIgnoreStart
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable;

use Magento\Catalog\Model\Product;

/**
 * Interface ConfigAdapterInterface
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product\Option\Configuration\Configurable
 */
interface ConfigAdapterInterface
{
    // @codingStandardsIgnoreEnd
    /**
     * Retrieve options config
     *
     * @return array
     */
    public function getOptions();
}
