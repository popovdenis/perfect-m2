<?php
namespace Aheadworks\EventTickets\Model\Product\Inventory\Indexer;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\InventoryIndexer\Indexer\IndexStructure;

class SelectBuilder
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Prepare select to create dummy records for AW event ticket products
     *
     * @param int $stockId
     * @return Select
     */
    public function execute(int $stockId): Select
    {
        return $this
            ->resourceConnection
            ->getConnection()
            ->select()
            ->from(
                [
                    'product_entity' => $this->resourceConnection->getTableName('catalog_product_entity')
                ],
                [
                    IndexStructure::SKU => 'product_entity.sku',
                    IndexStructure::QUANTITY => new \Zend_Db_Expr('1'),
                    IndexStructure::IS_SALABLE => new \Zend_Db_Expr('1'),
                ]
            )->where(
                'product_entity.' . ProductInterface::TYPE_ID . ' = (?)',
                EventTicket::TYPE_CODE
            )->group(
                [
                    'product_entity.sku'
                ]
            );
    }
}
