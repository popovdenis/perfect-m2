<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Space;

use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Model\Source\VenueList;
use Aheadworks\EventTickets\Model\Space;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Space as ResourceSpace;
use Magento\Framework\DB\Select;
use Aheadworks\EventTickets\Model\StorefrontLabelsResolver;
use Aheadworks\EventTickets\Model\ResourceModel\Sector\Collection as SectorCollection;
use Aheadworks\EventTickets\Model\ResourceModel\Sector\CollectionFactory as SectorCollectionFactory;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractCollection
    as StorefrontLabelsEntityAbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Space
 */
class Collection extends StorefrontLabelsEntityAbstractCollection
{
    /**#@+
     * Constants defined for additional grid columns
     */
    const VENUE_TITLE_COLUMN_NAME = 'venue_title';
    const SECTORS_QTY_COLUMN_NAME = 'sectors_qty';
    /**#@-*/

    /**
     * @var string
     */
    protected $_idFieldName = SpaceInterface::ID;

    /**
     * @var SectorCollectionFactory
     */
    private $sectorCollectionFactory;

    /**
     * @var bool
     */
    private $attachSectors = true;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param StorefrontLabelsResolver $storefrontLabelsResolver
     * @param SectorCollectionFactory $sectorCollectionFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        StorefrontLabelsResolver $storefrontLabelsResolver,
        SectorCollectionFactory $sectorCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->sectorCollectionFactory = $sectorCollectionFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storefrontLabelsResolver,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Space::class, ResourceSpace::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap(SpaceInterface::STATUS, 'main_table.status');

        return $this;
    }

    /**
     * Set flag for attaching sectors data
     *
     * @param bool $attachSectors
     * @return $this
     */
    public function setAttachSectors($attachSectors)
    {
        $this->attachSectors = $attachSectors;
        return $this;
    }

    /**
     * Get flag for attaching sectors data
     *
     * @return bool
     */
    public function getAttachSectors()
    {
        return $this->attachSectors;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachVenueTitleColumn();
        $this->attachSectorsQtyColumn();
        $this->attachSectorsIfNeeded();
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::addOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function unshiftOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::unshiftOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, [self::SECTORS_QTY_COLUMN_NAME])) {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter(self::SECTORS_QTY_COLUMN_NAME)) {
            $this->joinSectorsQtyColumn();
        }
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    protected function getStorefrontLabelsEntityType()
    {
        return SpaceInterface::STOREFRONT_LABELS_ENTITY_TYPE;
    }

    /**
     * Join fields for sort order
     *
     * @param string $field
     * @return $this
     */
    private function joinFieldsForSortOrder($field)
    {
        if ($field == self::VENUE_TITLE_COLUMN_NAME) {
            $this->joinVenueTitleColumn();
        }
        if ($field == self::SECTORS_QTY_COLUMN_NAME) {
            $this->joinSectorsQtyColumn();
        }
        return $this;
    }

    /**
     * Attach venue title column
     *
     * @return $this
     */
    private function attachVenueTitleColumn()
    {
        return $this->attachRelationTable(
            'aw_et_venue',
            SpaceInterface::VENUE_ID,
            VenueInterface::ID,
            VenueInterface::NAME,
            self::VENUE_TITLE_COLUMN_NAME,
            [],
            [],
            false,
            [VenueInterface::ID => VenueList::ANY_VENUE, VenueInterface::NAME => __('Any Venue')]
        );
    }

    /**
     * Join venue title column for filtering
     *
     * @return $this
     */
    private function joinVenueTitleColumn()
    {
        return $this->joinLinkageTable(
            'aw_et_venue',
            SpaceInterface::VENUE_ID,
            VenueInterface::ID,
            self::VENUE_TITLE_COLUMN_NAME,
            VenueInterface::NAME
        );
    }

    /**
     * Retrieve query for sectors qty calculation
     *
     * @return Select
     */
    private function getSectorsQtyQuery()
    {
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable('aw_et_sector')],
                ['sectors_qty' => new \Zend_Db_Expr('COUNT(*)'), SectorInterface::SPACE_ID]
            )->group(SectorInterface::SPACE_ID);

        return $select;
    }

    /**
     * Attach sectors qty column
     *
     * @return $this
     */
    private function attachSectorsQtyColumn()
    {
        return $this->attachRelationTable(
            $this->getSectorsQtyQuery(),
            SpaceInterface::ID,
            SectorInterface::SPACE_ID,
            'sectors_qty',
            self::SECTORS_QTY_COLUMN_NAME
        );
    }

    /**
     * Join sectors qty column for filtering
     *
     * @return $this
     */
    private function joinSectorsQtyColumn()
    {
        return $this->joinLinkageTable(
            $this->getSectorsQtyQuery(),
            SpaceInterface::ID,
            SectorInterface::SPACE_ID,
            self::SECTORS_QTY_COLUMN_NAME,
            'sectors_qty'
        );
    }

    /**
     * Attach sectors data column if corresponding flag is set
     *
     * @return $this
     */
    private function attachSectorsIfNeeded()
    {
        if ($this->getAttachSectors()) {
            $spaceIds = $this->getColumnValues(SpaceInterface::ID);
            if (!empty($spaceIds)) {
                /** @var SectorCollection $sectorCollection */
                $sectorCollection = $this->sectorCollectionFactory->create()
                    ->setStoreId($this->getStoreId())
                    ->addFieldToFilter(SectorInterface::SPACE_ID, ['in' => $spaceIds])
                    ->setOrder(SectorInterface::SORT_ORDER, self::SORT_ORDER_ASC);
                ;
                $groupedSectors = $sectorCollection->getItemsGroupedByColumn(SectorInterface::SPACE_ID);

                foreach ($this as $item) {
                    $itemId = $item->getData(SpaceInterface::ID);
                    if (isset($groupedSectors[$itemId])) {
                        $item->setData(SpaceInterface::SECTORS, $groupedSectors[$itemId]);
                    }
                }
            }
        }
        return $this;
    }
}
