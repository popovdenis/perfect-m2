<?php
namespace Aheadworks\EventTickets\Model\Stock\Item\Configuration;

use Aheadworks\EventTickets\Model\ThirdParty\Module\Manager;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;

/**
 * Class Factory
 * @package Aheadworks\EventTickets\Model\Stock\Item\Configuration
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Manager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Create class instance with according to enabled or disabled Magento_Inventory extension
     *
     * @param string $productSku
     * @param StockInterface|StockRegistryInterface $stock
     * @param int $websiteId
     *
     * @return GetStockItemConfigurationInterface|StockItemInterface|null
     */
    public function create($productSku, $stock, $websiteId)
    {
        if ($stock instanceof StockInterface) {
            /** @var GetStockItemConfigurationInterface $stockItemConfiguration */
            $stockItemConfiguration = $this->objectManager->create(GetStockItemConfigurationInterface::class);
            try {
                $stockItemConfiguration = $stockItemConfiguration->execute($productSku, $stock->getStockId());
            } catch (LocalizedException $e) {
                $stockItemConfiguration = null;
            }
        } else {
            try {
                $stockItemConfiguration = $stock->getStockItemBySku($productSku, $websiteId);
            } catch (NoSuchEntityException $e) {
                $stockItemConfiguration = null;
            }
        }

        return $stockItemConfiguration;
    }
}
