<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Event\AddFilterStrategy;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class AddStatusFilterToCollection
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Event\AddFilterStrategy
 */
class AddStatusFilterToCollection implements AddFilterToCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        if (isset($condition['eq']) && $condition['eq']) {
            /** @var \Aheadworks\EventTickets\Model\ResourceModel\Product\Collection $collection */
            $collection->addStatusFilter($condition['eq']);
        }
    }
}
