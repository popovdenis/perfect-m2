<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor\EndDate;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\CollectionFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Collection;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * Class ProductIdsResolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor\EndDate
 */
class ProductIdsResolver
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieve product ids for which the deadline has arrived
     *
     * @return array
     */
    public function resolve()
    {
        return $this->getProductIdsToProcess();
    }

    /**
     * Retrieve products ids to process
     *
     * @return array[]
     */
    private function getProductIdsToProcess()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $endDate = ProductAttributeInterface::CODE_AW_ET_END_DATE;

        $collection->addAttributeToFilter(ProductInterface::STATUS, ProductStatus::STATUS_ENABLED);
        $collection->addAttributeToSelect($endDate, 'left');
        $whereCondition = $collection->getConnection()->quoteInto(
            'at_' . $endDate . '.value < ?',
            new \Zend_Db_Expr('NOW()')
        );
        $collection->getSelect()->where($whereCondition);

        return $collection->getAllIds();
    }
}
