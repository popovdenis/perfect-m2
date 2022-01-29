<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor;

use Aheadworks\EventTickets\Model\Ticket\Processor\EndDate\ProductIdsResolver;
use Aheadworks\EventTickets\Model\Ticket\Processor\EndDate\ProductUpdater;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\EventTickets\Model\Config;

/**
 * Class EndDate
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor
 */
class EndDate implements IdentityInterface
{
    /**
     * @var ProductIdsResolver
     */
    private $productIdsResolver;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $processedProductIds = [];

    /**
     * @param ProductIdsResolver $productIdsResolver
     * @param EventManagerInterface $eventManager
     * @param ProductUpdater $productUpdater
     * @param Config $config
     */
    public function __construct(
        ProductIdsResolver $productIdsResolver,
        EventManagerInterface $eventManager,
        ProductUpdater $productUpdater,
        Config $config
    ) {
        $this->productIdsResolver = $productIdsResolver;
        $this->eventManager = $eventManager;
        $this->productUpdater = $productUpdater;
        $this->config = $config;
    }

    /**
     * Processing events on end date coming
     *
     * @return void
     */
    public function process()
    {
        if ($this->config->isPastEventsMustBeHidden()) {
            $this->processedProductIds = $this->productIdsResolver->resolve();
            $this->productUpdater->disableProducts($this->processedProductIds);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->processedProductIds as $productId) {
            $identities[] = Product::CACHE_TAG . '_' . $productId;
        }

        return $identities;
    }
}
