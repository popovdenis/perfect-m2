<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\HandlerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class RecurringSchedule
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product
 */
class RecurringSchedule extends AbstractDb
{
    /**
     * Main table name
     */
    const MAIN_TABLE = 'aw_et_product_recurring_schedule';

    /**
     * Time slots table name
     */
    const TIME_SLOTS_TABLE_NAME = 'aw_et_product_recurring_time_slot';

    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = ProductRecurringScheduleInterface::ID;

    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @param Context $context
     * @param array $handlers
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        array $handlers = [],
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, ProductRecurringScheduleInterface::ID);
    }

    /**
     * Delete by product id
     *
     * @param int $productId
     */
    public function deleteByProductId($productId)
    {
        $this->getConnection()->delete(
            $this->getTable(self::MAIN_TABLE),
            [ProductRecurringScheduleInterface::PRODUCT_ID . ' = ?' => $productId]
        );
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof HandlerInterface) {
                $handler->save($object);
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof HandlerInterface) {
                $handler->load($object);
            }
        }
        return parent::_afterLoad($object);
    }
}
