<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket\Validator;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class IsUnique
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket\Validator
 */
class IsUnique
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Check unique ticket number
     *
     * @param string $number
     * @return bool
     * @throws \Exception
     */
    public function validate($number)
    {
        $ticketMetaData = $this->metadataPool->getMetadata(TicketInterface::class);
        $connection = $this->resourceConnection->getConnectionByName($ticketMetaData->getEntityConnectionName());

        $select = $connection->select()
            ->from($this->resourceConnection->getTableName($ticketMetaData->getEntityTable()))
            ->where('number = :number');
        if ($connection->fetchRow($select, ['number' => $number])) {
            return false;
        }

        return true;
    }
}
