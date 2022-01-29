<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor;

use Aheadworks\EventTickets\Model\Ticket\Processor\PriceUpdate\ProductIdsResolver;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class PriceUpdate
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor
 */
class PriceUpdate implements IdentityInterface
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
     * @param ProductIdsResolver $productIdsResolver
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        ProductIdsResolver $productIdsResolver,
        EventManagerInterface $eventManager
    ) {
        $this->productIdsResolver = $productIdsResolver;
        $this->eventManager = $eventManager;
    }

    /**
     * Processing events on deadline
     *
     * @return void
     */
    public function process()
    {
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        $productIds = $this->productIdsResolver->resolve();
        foreach ($productIds as $productId) {
            $identities[] = Product::CACHE_TAG . '_' . $productId;
        }

        return $identities;
    }
}
