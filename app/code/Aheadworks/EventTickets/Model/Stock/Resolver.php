<?php
namespace Aheadworks\EventTickets\Model\Stock;

use Aheadworks\EventTickets\Model\Stock\Resolver\Factory as StockResolverFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Stock
 */
class Resolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockResolverFactory
     */
    private $stockResolverFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StockResolverFactory $stockResolverFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StockResolverFactory $stockResolverFactory
    ) {
        $this->storeManager = $storeManager;
        $this->stockResolverFactory = $stockResolverFactory;
    }

    /**
     * Resolve stock instance by website id
     *
     * @param int $websiteId
     * @return StockResolverInterface|StockRegistryInterface|null
     */
    public function getByWebsiteId($websiteId)
    {
        try {
            $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
           return $this->stockResolverFactory->create($websiteCode);
        } catch (LocalizedException $exception) {
            return null;
        }
    }
}
