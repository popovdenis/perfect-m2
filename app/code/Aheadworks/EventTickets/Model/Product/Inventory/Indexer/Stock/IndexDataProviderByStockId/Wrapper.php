<?php
namespace Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock\IndexDataProviderByStockId;

use Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock\IndexDataProviderByStockIdInterface;
use Magento\InventoryIndexer\Indexer\Stock\IndexDataProviderByStockId;

class Wrapper extends IndexDataProviderByStockId implements IndexDataProviderByStockIdInterface
{
    /**
     * @var Factory
     */
    private $indexDataProviderByStockIdFactory;

    /**
     * @param Factory $indexDataProviderByStockIdFactory
     */
    public function __construct(
        Factory $indexDataProviderByStockIdFactory
    ) {
        $this->indexDataProviderByStockIdFactory = $indexDataProviderByStockIdFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(int $stockId): \ArrayIterator
    {
        $indexDataProviderByStockId = $this->indexDataProviderByStockIdFactory->getInstance();
        return $indexDataProviderByStockId->execute($stockId);
    }
}
