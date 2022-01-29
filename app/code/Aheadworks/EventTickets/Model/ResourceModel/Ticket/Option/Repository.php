<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket\Option;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class Repository
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket\Option
 */
class Repository
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param $productId
     * @return array
     * @throws \Exception
     */
    public function getTicketOptionsListByProduct($productId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(
                ['ticket_option' => $this->getTicketOptionTableName()],
                [
                    TicketOptionInterface::NAME,
                    TicketOptionInterface::TYPE,
                    TicketOptionInterface::KEY
                ]
            )->joinLeft(
                ['ticket' => $this->getTicketTableName()],
                'ticket.' . TicketInterface::ID . ' = ticket_option.ticket_id',
                [
                    'product_id' => 'ticket.product_id'
                ]
            )->where(
                'product_id = :product_id'
            )->group(
                TicketOptionInterface::NAME
            );
        $currentTicketOptionsList = $connection->fetchAll($select, ['product_id' => $productId]);
        return $currentTicketOptionsList;
    }

    /**
     * Retrieve all values for specified option key
     *
     * @param $optionKey
     * @return array
     * @throws \Exception
     */
    public function getAllValuesByOptionKey($optionKey)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(
                ['ticket_option' => $this->getTicketOptionTableName()],
                [
                    TicketOptionInterface::VALUE,
                ]
            )->where(
                'ticket_option.key = :option_key'
            )->group(
                'ticket_option.value'
            )
        ;
        $valuesForOptionKey = $connection->fetchAll($select, ['option_key' => $optionKey]);
        return $valuesForOptionKey;
    }

    /**
     * Retrieve ticket option table
     *
     * @return string
     * @throws \Exception
     */
    private function getTicketOptionTableName()
    {
        return $this->metadataPool->getMetadata(TicketOptionInterface::class)->getEntityTable();
    }

    /**
     * Retrieve ticket table name
     *
     * @return string
     * @throws \Exception
     */
    private function getTicketTableName()
    {
        return $this->metadataPool->getMetadata(TicketInterface::class)->getEntityTable();
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TicketOptionInterface::class)->getEntityConnectionName()
        );
    }
}
