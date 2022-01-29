<?php
namespace Aheadworks\EventTickets\Model\Stock\Item\Configuration;

use Aheadworks\EventTickets\Model\Stock\Resolver as StockResolver;
use Aheadworks\EventTickets\Model\Stock\Item\Configuration\Factory as GetStockItemConfigurationFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Stock\Item\Configuration
 */
class Resolver
{
    /**
     * @var StockResolver
     */
    private $stockResolver;

    /**
     * @var GetStockItemConfigurationFactory
     */
    private $getStockItemConfigurationFactory;

    /**
     * @param StockResolver $stockResolver
     * @param GetStockItemConfigurationFactory $getStockItemConfigurationFactory
     */
    public function __construct(
        StockResolver $stockResolver,
        GetStockItemConfigurationFactory $getStockItemConfigurationFactory
    ) {
        $this->stockResolver = $stockResolver;
        $this->getStockItemConfigurationFactory = $getStockItemConfigurationFactory;
    }

    /**
     * Retrieve stock item configuration for the product within specific website
     *
     * @param string $productSku
     * @param int $websiteId
     * @return StockItemInterface|GetStockItemConfigurationInterface|null
     */
    public function getForProduct($productSku, $websiteId)
    {
        $stock = $this->stockResolver->getByWebsiteId($websiteId);

        return $stock
            ? $this->getStockItemConfigurationFactory->create($productSku, $stock, $websiteId)
            : null;
    }
}
