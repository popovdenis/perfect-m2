<?php
namespace Aheadworks\EventTickets\Model\Ticket\Processor\PriceUpdate;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\CollectionFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Collection;

/**
 * Class ProductIdsResolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Processor\PriceUpdate
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
     * Retrieve product ids for which price should be updated
     *
     * @return array
     */
    public function resolve()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $earlyBirdAttr = ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE;
        $lastDaysAttr = ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE;

        $collection->addAttributeToSelect($earlyBirdAttr, 'left');
        $collection->addAttributeToSelect($lastDaysAttr, 'left');
        $condition1 = $collection->getConnection()->quoteInto(
            'at_' . $earlyBirdAttr . '.value = ?',
            new \Zend_Db_Expr('CURDATE()')
        );
        $condition2 = $collection->getConnection()->quoteInto(
            'at_' . $lastDaysAttr . '.value = ?',
            new \Zend_Db_Expr('CURDATE()')
        );
        $collection->getSelect()->where($condition1 . ' OR ' . $condition2);

        return $collection->getAllIds();
    }
}
