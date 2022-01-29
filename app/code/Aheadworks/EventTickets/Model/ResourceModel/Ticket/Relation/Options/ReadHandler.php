<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket\Relation\Options;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket\Relation\Options
 */
class ReadHandler implements ExtensionInterface
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketOptionInterfaceFactory
     */
    private $ticketOptionFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketOptionInterfaceFactory $ticketOptionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        DataObjectHelper $dataObjectHelper,
        TicketOptionInterfaceFactory $ticketOptionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketOptionFactory = $ticketOptionFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var TicketInterface $entity */
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $options = $this->getOptionObjects($this->getOptions($entity->getId()));
        $entity->setOptions($options);

        return $entity;
    }

    /**
     * Retrieve options
     *
     * @param int $ticketId
     * @return array
     * @throws \Exception
     */
    private function getOptions($ticketId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('aw_et_ticket_option'))
            ->where('ticket_id = :ticket_id');

        return $connection->fetchAll($select, ['ticket_id' => $ticketId]);
    }

    /**
     * Retrieve storefront labels from data array
     *
     * @param array $options
     * @return TicketOptionInterface[]
     */
    private function getOptionObjects($options)
    {
        $objects = [];
        foreach ($options as $option) {
            /** @var TicketOptionInterface $ticketOption */
            $ticketOption = $this->ticketOptionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $ticketOption,
                $option,
                TicketOptionInterface::class
            );
            $objects[] = $ticketOption;
        }
        return $objects;
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
