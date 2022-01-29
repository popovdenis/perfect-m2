<?php
namespace Aheadworks\EventTickets\Model\Stock\Resolver;

use Aheadworks\EventTickets\Model\ThirdParty\Module\Manager;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

/**
 * Class Factory
 * @package Aheadworks\EventTickets\Model\Stock\Resolver
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
     * @param string $websiteCode
     * @return StockInterface|StockRegistryInterface|null
     */
    public function create($websiteCode)
    {
        return $this->moduleManager->isMagentoMsiModuleEnabled()
            ? $this->objectManager->create(StockResolverInterface::class)
                ->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)
            : $this->objectManager->create(StockRegistryInterface::class);
    }
}
