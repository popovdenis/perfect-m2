<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

/**
 * Class SpaceId
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class SpaceId extends VenueId
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        return parent::validate($object);
    }
}
