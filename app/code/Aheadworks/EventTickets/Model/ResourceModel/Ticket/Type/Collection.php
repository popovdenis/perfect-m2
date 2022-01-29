<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type;

use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Model\Ticket\Type as TicketType;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type as ResourceTicketType;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\AbstractCollection
    as StorefrontLabelsEntityAbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type
 */
class Collection extends StorefrontLabelsEntityAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = TicketTypeInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(TicketType::class, ResourceTicketType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStorefrontLabelsEntityType()
    {
        return TicketTypeInterface::STOREFRONT_LABELS_ENTITY_TYPE;
    }
}
