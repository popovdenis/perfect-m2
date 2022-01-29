<?php
namespace Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock;

interface IndexDataProviderByStockIdInterface
{
    /**
     * Returns selected data
     *
     * @param int $stockId
     * @throws \Exception
     * @return \ArrayIterator
     */
    public function execute(int $stockId): \ArrayIterator;
}
