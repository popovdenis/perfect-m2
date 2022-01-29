<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\TicketType;

use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\TicketType
 */
class ListingDataProvider extends AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
