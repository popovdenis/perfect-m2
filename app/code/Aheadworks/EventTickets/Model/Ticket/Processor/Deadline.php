<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor;

use Aheadworks\EventTickets\Model\Ticket\Processor\Deadline\ProductIdsResolver;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Deadline
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor
 */
class Deadline implements IdentityInterface
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
        $deadlineProductIds = $this->productIdsResolver->resolve();
        foreach ($deadlineProductIds as $deadlineProductId) {
            $identities[] = Product::CACHE_TAG . '_' . $deadlineProductId;
        }

        return $identities;
    }
}
