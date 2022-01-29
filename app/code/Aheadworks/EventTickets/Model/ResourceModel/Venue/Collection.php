<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Venue;

use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Model\Venue;
use Aheadworks\EventTickets\Model\ResourceModel\Venue as ResourceVenue;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractCollection
    as StorefrontLabelsEntityAbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Venue
 */
class Collection extends StorefrontLabelsEntityAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = VenueInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Venue::class, ResourceVenue::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStorefrontLabelsEntityType()
    {
        return VenueInterface::STOREFRONT_LABELS_ENTITY_TYPE;
    }
}
