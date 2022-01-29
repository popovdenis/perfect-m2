<?php
namespace Aheadworks\EventTickets\Model\ResourceModel;

use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractResourceModel
    as StorefrontLabelsEntityAbstractResourceModel;

/**
 * Class Sector
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel
 */
class Sector extends StorefrontLabelsEntityAbstractResourceModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_et_sector', 'id');
    }
}
