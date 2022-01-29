<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product;

use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface;
use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\CriteriaInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\EventTickets\Model\Product\Sector\Calculator\Ticket as TicketCalculator;

/**
 * Class SectorRepository
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product
 */
class SectorRepository
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
     * @var TicketCalculator
     */
    private $ticketCalculator;

    /**
     * @var string
     */
    private $sectorTicketsTableName;

    /**
     * @var string
     */
    private $sectorTicketsOptionsTableName;

    /**
     * @var string
     */
    private $sectorProductsTableName;

    /**
     * @var string
     */
    private $productSectorTableName;

    /**
     * @var string
     */
    private $sectorTableName;

    /**
     * @var string
     */
    private $spaceTableName;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param TicketCalculator $ticketCalculator
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        TicketCalculator $ticketCalculator
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->ticketCalculator = $ticketCalculator;
        $this->sectorTicketsTableName =
            $this->resourceConnection->getTableName('aw_et_product_sector_tickets');
        $this->sectorTicketsOptionsTableName =
            $this->resourceConnection->getTableName('aw_et_product_sector_tickets_options');
        $this->sectorProductsTableName =
            $this->resourceConnection->getTableName('aw_et_product_sector_products');
        $this->productSectorTableName = $this->resourceConnection->getTableName('aw_et_product_sector');
        $this->sectorTableName = $this->resourceConnection->getTableName('aw_et_sector');
        $this->spaceTableName = $this->resourceConnection->getTableName('aw_et_space');
    }

    /**
     * Save product sector data
     *
     * @param ProductSectorInterface[] $productSectors
     * @param ProductInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function save($productSectors, $entity)
    {
        $ticketsData = $ticketOptionsData = [];
        $entityId = $entity->getId();
        foreach ($productSectors as $productSector) {
            $sectorsData = ['product_id' => $entityId, 'sector_id' => $productSector->getSectorId()];
            $this->getConnection()->insert($this->getTableName(), $sectorsData);
            $lastInsertId = $this->getConnection()->lastInsertId();

            /** @var ProductSectorTicketInterface $ticket */
            foreach ($productSector->getSectorTickets() as $ticket) {
                $uid = uniqid();
                $ticketsData[] = [
                    ProductSectorTicketInterface::PRODUCT_SECTOR_ID => $lastInsertId,
                    ProductSectorTicketInterface::UID => $uid,
                    ProductSectorTicketInterface::TYPE_ID => $ticket->getTypeId(),
                    ProductSectorTicketInterface::EARLY_BIRD_PRICE => $ticket->getEarlyBirdPrice() ?: null,
                    ProductSectorTicketInterface::PRICE => $ticket->getPrice(),
                    ProductSectorTicketInterface::LAST_DAYS_PRICE => $ticket->getLastDaysPrice() ?: null,
                    ProductSectorTicketInterface::POSITION => $ticket->getPosition()
                ];
                foreach ($ticket->getPersonalOptionUids() as $optionUId) {
                    if (!empty($optionUId)) {
                        $ticketOptionsData[] = [
                            ProductSectorTicketInterface::PRODUCT_SECTOR_TICKET_UID => $uid,
                            ProductSectorTicketInterface::PRODUCT_OPTION_UID => $optionUId
                        ];
                    }
                }
            }

            if ($productSector->getSectorProducts()) {
                /** @var ProductSectorProductInterface $product */
                foreach ($productSector->getSectorProducts() as $product) {
                    $productsData[] = [
                        ProductSectorProductInterface::PRODUCT_SECTOR_ID => $lastInsertId,
                        ProductSectorProductInterface::PRODUCT_ID => $product->getProductId(),
                        ProductSectorProductInterface::POSITION => $product->getPosition(),
                    ];
                }
            }
        }
        if (!empty($ticketsData)) {
            $this->getConnection()->insertMultiple($this->sectorTicketsTableName, $ticketsData);
            if (!empty($ticketOptionsData)) {
                $this->getConnection()->insertMultiple($this->sectorTicketsOptionsTableName, $ticketOptionsData);
            }
        }
        if (!empty($productsData)) {
            $this->getConnection()->insertMultiple($this->sectorProductsTableName, $productsData);
        }

        return true;
    }

    /**
     * Retrieve product sector data by product id
     *
     * @param int $productId
     * @return array
     * @throws \Exception
     */
    public function getByProductId($productId)
    {
        $connection = $this->getConnection();
        $sectorTableJoinCondition = [
            'sector.id =' . $this->getTableName() . '.sector_id',
            'sector.status =' . Status::STATUS_ENABLED
        ];
        $spaceTableJoinCondition = [
            'space.id = sector.space_id',
            'space.status =' . Status::STATUS_ENABLED
        ];
        $sectorSelect = $connection->select()
            ->from($this->getTableName(), [ProductSectorInterface::ID, ProductSectorInterface::SECTOR_ID])
            ->join(
                ['sector' => $this->sectorTableName],
                implode(' AND ', $sectorTableJoinCondition),
                []
            )->join(
                ['space' => $this->spaceTableName],
                implode(' AND ', $spaceTableJoinCondition),
                []
            )->where(ProductSectorInterface::PRODUCT_ID . ' = :product_id')
            ->order('sector.sort_order ASC');
        $productSectors = $connection->fetchAll($sectorSelect, [ProductSectorInterface::PRODUCT_ID => $productId]);

        if (!empty($productSectors)) {
            $productSectorIds = $this->getProductSectorIds($productSectors);
            $tickets = $this->getByProductSectorIds($productSectorIds);
            $ticketOptions = $this->getTicketOptions($tickets);
            $products = $this->getSectorProducts($productSectorIds);
            $productSectors = $this->mapFields($productSectors, $tickets, $productId, $ticketOptions, $products);
        }

        return $productSectors;
    }

    /**
     * Delete all existed product sectors by product id
     *
     * @param int $productId
     * @return bool
     * @throws \Exception
     */
    public function deleteByProductId($productId)
    {
        $this->getConnection()
            ->delete($this->getTableName(), [ProductSectorInterface::PRODUCT_ID . ' = ?' => $productId]);
        return true;
    }

    /**
     * Retrieve sector ticket data by product sector ids
     *
     * @param array $productSectorIds
     * @return array
     * @throws \Exception
     */
    public function getByProductSectorIds($productSectorIds)
    {
        $connection = $this->getConnection();
        $ticketSelect = $connection->select()
            ->from(
                $this->sectorTicketsTableName,
                [
                    ProductSectorTicketInterface::PRODUCT_SECTOR_ID,
                    ProductSectorTicketInterface::UID,
                    ProductSectorTicketInterface::TYPE_ID,
                    ProductSectorTicketInterface::EARLY_BIRD_PRICE,
                    ProductSectorTicketInterface::PRICE,
                    ProductSectorTicketInterface::LAST_DAYS_PRICE,
                    ProductSectorTicketInterface::POSITION
                ]
            )->where(ProductSectorTicketInterface::PRODUCT_SECTOR_ID . ' IN (?)', $productSectorIds)
            ->order(ProductSectorTicketInterface::POSITION . ' ' . CriteriaInterface::SORT_ORDER_ASC);
        $tickets = $connection->fetchAll($ticketSelect);

        return $tickets;
    }

    /**
     * Retrieve sector product data by product sector ids
     *
     * @param array $productSectorIds
     * @return array
     * @throws \Exception
     */
    public function getSectorProducts($productSectorIds)
    {
        $connection = $this->getConnection();
        $productSelect = $connection->select()
            ->from(
                $this->sectorProductsTableName,
                [
                    ProductSectorProductInterface::PRODUCT_SECTOR_ID,
                    ProductSectorProductInterface::PRODUCT_ID,
                    ProductSectorProductInterface::POSITION,
                ]
            )->where(ProductSectorProductInterface::PRODUCT_SECTOR_ID . ' IN (?)', $productSectorIds);
        $products = $connection->fetchAll($productSelect);

        return $products;
    }

    /**
     * @param array $productIds
     * @param array $sectorIds
     * @return array
     * @throws \Exception
     */
    public function getAdditionalProductsByTicketSectorProducts($productIds, $sectorIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['etps' => $this->productSectorTableName], [])
            ->joinLeft(
                ['etpsp' => $this->sectorProductsTableName],
                'etps.id = etpsp.product_sector_id',
                [
                    ProductSectorProductInterface::PRODUCT_ID
                ]
            )
            ->where('etps.' . ProductSectorInterface::PRODUCT_ID . ' IN (?)', $productIds)
            ->where('etps.' . ProductSectorInterface::SECTOR_ID . ' IN (?)', $sectorIds);
        $products = $connection->fetchCol($select);

        return $products;
    }

    /**
     * Retrieve ticket options
     *
     * @param array $tickets
     * @return array
     * @throws \Exception
     */
    public function getTicketOptions($tickets)
    {
        $ticketUIds = $ticketOptions = [];
        foreach ($tickets as $ticket) {
            $ticketUIds[] = $ticket[ProductSectorTicketInterface::UID];
        }
        if ($ticketUIds) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(
                    $this->sectorTicketsOptionsTableName,
                    [
                        ProductSectorTicketInterface::PRODUCT_SECTOR_TICKET_UID,
                        ProductSectorTicketInterface::PRODUCT_OPTION_UID
                    ]
                )->where(ProductSectorTicketInterface::PRODUCT_SECTOR_TICKET_UID . ' IN (?)', $ticketUIds);
            $ticketOptions = $connection->fetchAll($select);
        }

        return $ticketOptions;
    }

    /**
     * Retrieve product sector tickets
     *
     * @param array $productSectors
     * @return array
     * @throws \Exception
     */
    private function getProductSectorIds($productSectors)
    {
        $productSectorIds = [];
        foreach ($productSectors as $productSector) {
            $productSectorIds[] = $productSector[ProductSectorInterface::ID];
        }

        return $productSectorIds;
    }

    /**
     * Map fields
     *
     * @param array $productSectors
     * @param array $tickets
     * @param int $productId
     * @param array $ticketOptions
     * @param array $products
     * @return array
     */
    private function mapFields($productSectors, $tickets, $productId, $ticketOptions, $products)
    {
        $sectorConfigMapped = [];
        foreach ($productSectors as $productSector) {
            $sectorId = $productSector[ProductSectorInterface::SECTOR_ID];
            $productSectorId = $productSector[ProductSectorInterface::ID];

            if (!isset($sectorConfigMapped[$sectorId])) {
                $sectorConfigMapped[$sectorId] = [
                    ProductSectorInterface::SECTOR_ID => $sectorId,
                    ProductSectorInterface::QTY_AVAILABLE_TICKETS =>
                        $this->ticketCalculator->getQtyAvailable($productId, $sectorId)
                ];
            }
            foreach ($tickets as $ticket) {
                if ($ticket[ProductSectorTicketInterface::PRODUCT_SECTOR_ID] != $productSectorId) {
                    continue;
                }
                $preparedTicketOptionUIds = [];
                foreach ($ticketOptions as $option) {
                    if ($option[ProductSectorTicketInterface::PRODUCT_SECTOR_TICKET_UID]
                        == $ticket[ProductSectorTicketInterface::UID]
                    ) {
                        $preparedTicketOptionUIds[] = $option[ProductSectorTicketInterface::PRODUCT_OPTION_UID];
                    }
                }
                $sectorConfigMapped[$sectorId][ProductSectorInterface::SECTOR_TICKETS][] = [
                    ProductSectorTicketInterface::TYPE_ID  => $ticket[ProductSectorTicketInterface::TYPE_ID],
                    ProductSectorTicketInterface::EARLY_BIRD_PRICE =>
                        $ticket[ProductSectorTicketInterface::EARLY_BIRD_PRICE],
                    ProductSectorTicketInterface::PRICE    => $ticket[ProductSectorTicketInterface::PRICE],
                    ProductSectorTicketInterface::LAST_DAYS_PRICE =>
                        $ticket[ProductSectorTicketInterface::LAST_DAYS_PRICE],
                    ProductSectorTicketInterface::POSITION => $ticket[ProductSectorTicketInterface::POSITION],
                    ProductSectorTicketInterface::PERSONAL_OPTION_UIDS => $preparedTicketOptionUIds
                ];
            }

            $sectorConfigMapped[$sectorId][ProductSectorInterface::SECTOR_PRODUCTS] = [];
            foreach ($products as $product) {
                if ($product[ProductSectorProductInterface::PRODUCT_SECTOR_ID] != $productSectorId) {
                    continue;
                }
                $sectorConfigMapped[$sectorId][ProductSectorInterface::SECTOR_PRODUCTS][] = [
                    ProductSectorProductInterface::PRODUCT_ID  => $product[ProductSectorProductInterface::PRODUCT_ID],
                    ProductSectorProductInterface::POSITION    => $product[ProductSectorProductInterface::POSITION],
                ];
            }
        }

        return array_values($sectorConfigMapped);
    }

    /**
     * Retrieve table name
     *
     * @return string
     * @throws \Exception
     */
    private function getTableName()
    {
        return $this->metadataPool->getMetadata(ProductSectorInterface::class)->getEntityTable();
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
            $this->metadataPool->getMetadata(ProductSectorInterface::class)->getEntityConnectionName()
        );
    }
}
