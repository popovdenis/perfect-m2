<?php
namespace Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock\IndexDataProviderByStockId;

use Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock\IndexDataProviderByStockIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryIndexer\Indexer\SelectBuilderInterface;
use Aheadworks\EventTickets\Model\Product\Inventory\Indexer\SelectBuilder
    as EventTicketsInventoryIndexerSelectBuilder;

class InventoryIndexerVersion210 implements IndexDataProviderByStockIdInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var SelectBuilderInterface[]|EventTicketsInventoryIndexerSelectBuilder[]
     */
    private $selectBuilderList;

    /**
     * @param ResourceConnection $resourceConnection
     * @param SelectBuilderInterface[]|EventTicketsInventoryIndexerSelectBuilder[] $selectBuilderList
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        array $selectBuilderList = []
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->selectBuilderList = $selectBuilderList;
    }

    /**
     * @inheritDoc
     */
    public function execute(int $stockId): \ArrayIterator
    {
        $result = [];
        $connection = $this->resourceConnection->getConnection();

        foreach ($this->selectBuilderList as $selectBuilder) {
            if ($selectBuilder instanceof SelectBuilderInterface
                || $selectBuilder instanceof EventTicketsInventoryIndexerSelectBuilder
            ) {
                $select = $selectBuilder->execute($stockId);
                $result[] = $connection->fetchAll($select);
            }
        }

        return new \ArrayIterator(array_merge([], ...$result));
    }
}
