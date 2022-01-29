<?php
namespace Aheadworks\EventTickets\Model\ResourceModel;

use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractResourceModel
    as StorefrontLabelsEntityAbstractResourceModel;

/**
 * Class Venue
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel
 */
class Venue extends StorefrontLabelsEntityAbstractResourceModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_et_venue', 'id');
    }
}
