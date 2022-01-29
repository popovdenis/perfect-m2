<?php
namespace Aheadworks\EventTickets\Model\ResourceModel;

use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractResourceModel
    as StorefrontLabelsEntityAbstractResourceModel;

/**
 * Class Space
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel
 */
class Space extends StorefrontLabelsEntityAbstractResourceModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_et_space', 'id');
    }
}
