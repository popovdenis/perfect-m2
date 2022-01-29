<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Sector;

use Aheadworks\EventTickets\Model\Sector;
use Aheadworks\EventTickets\Model\ResourceModel\Sector as ResourceSector;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractCollection
    as StorefrontLabelsEntityAbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Sector
 */
class Collection extends StorefrontLabelsEntityAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = SectorInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Sector::class, ResourceSector::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStorefrontLabelsEntityType()
    {
        return SectorInterface::STOREFRONT_LABELS_ENTITY_TYPE;
    }
}
