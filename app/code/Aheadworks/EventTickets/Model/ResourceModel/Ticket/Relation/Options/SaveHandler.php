<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket\Relation\Options;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Option\Resolver as TicketOptionResolver;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket\Relation\Options
 */
class SaveHandler implements ExtensionInterface
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
     * @var TicketOptionResolver
     */
    private $ticketOptionResolver;

    /**
     * @var string
     */
    private $ticketOptionTableName;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param TicketOptionResolver $ticketOptionResolver
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        TicketOptionResolver $ticketOptionResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->ticketOptionResolver = $ticketOptionResolver;
        $this->ticketOptionTableName = $this->resourceConnection->getTableName('aw_et_ticket_option');
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var TicketInterface $entity */
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $this->deleteByEntity($entity->getId());
        $options = $this->getOptions($entity);
        $this->insertOptionsData($options);

        return $entity;
    }

    /**
     * Remove options by ticket id
     *
     * @param int $ticketId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($ticketId)
    {
        return $this->getConnection()->delete($this->ticketOptionTableName, ['ticket_id = ?' => $ticketId]);
    }

    /**
     * Retrieve array of current options data to insert
     *
     * @param TicketInterface $entity
     * @return array
     */
    private function getOptions($entity)
    {
        $optionsData = [];
        $options = $entity->getOptions();
        if (!empty($options)) {
            foreach ($options as $option) {
                $optionsData[] = [
                    'ticket_id' => (int)$entity->getId(),
                    'name'      => $option->getName(),
                    'type'      => $option->getType(),
                    'value'     => $option->getValue(),
                    'key'       => $this->ticketOptionResolver->generateOptionKey($option)
                ];
            }
        }
        return $optionsData;
    }

    /**
     * Insert current options data
     *
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    private function insertOptionsData($options)
    {
        if (!empty($options)) {
            $this->getConnection()->insertMultiple($this->ticketOptionTableName, $options);
        }
        return $this;
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
            $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
        );
    }
}
