<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Recurring\Event;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Recurring\Grid\Collection as EventProductCollection;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Recurring\Grid\CollectionFactory as EventProductCollectionFactory;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Event
 */
class ListingDataProvider extends ProductDataProvider
{
    /**
     * @var EventProductCollection
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ProductCollectionFactory $collectionFactory
     * @param EventProductCollectionFactory $eventProductCollectionFactory
     * @param AddFieldToCollectionInterface[] $addFieldStrategies
     * @param AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ProductCollectionFactory $collectionFactory,
        EventProductCollectionFactory $eventProductCollectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->collection = $eventProductCollectionFactory->create();
    }
}
