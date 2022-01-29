<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Event\AddFieldStrategy;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class AddStatusFieldToCollection implements AddFieldToCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        /** @var \Aheadworks\EventTickets\Model\ResourceModel\Product\Collection $collection */
        $collection->setNeedToAddEventStatus(true);
    }
}
