<?php
namespace Aheadworks\EventTickets\Model\Product;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Product\RecurringSchedule as RecurringScheduleResourceModel;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class RecurringSchedulePersistor
 * @package Aheadworks\EventTickets\Model\Product
 */
class RecurringSchedulePersistor
{
    /**
     * @var RecurringScheduleResourceModel
     */
    private $resource;

    /**
     * @var ProductRecurringScheduleInterfaceFactory
     */
    private $productRecurringScheduleFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByProduct = [];

    /**
     * @param RecurringScheduleResourceModel $resource
     * @param ProductRecurringScheduleInterfaceFactory $productRecurringScheduleFactory
     */
    public function __construct(
        RecurringScheduleResourceModel $resource,
        ProductRecurringScheduleInterfaceFactory $productRecurringScheduleFactory
    ) {
        $this->resource = $resource;
        $this->productRecurringScheduleFactory = $productRecurringScheduleFactory;
    }

    /**
     * Save recurring schedule
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return ProductRecurringScheduleInterface
     * @throws CouldNotSaveException
     */
    public function save(ProductRecurringScheduleInterface $recurringSchedule)
    {
        try {
            $this->resource->save($recurringSchedule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $recurringSchedule;
    }

    /**
     * Retrieve by id
     *
     * @param int $id
     * @return ProductRecurringScheduleInterface
     */
    public function get($id)
    {
        if (!isset($this->registry[$id])) {
            /** @var ProductRecurringScheduleInterface $recurringSchedule */
            $recurringSchedule = $this->productRecurringScheduleFactory->create();
            $this->resource->load($recurringSchedule, $id);
            $this->registry[$id] = $recurringSchedule;
            $this->registryByProduct[$recurringSchedule->getProductId()] = $recurringSchedule;
        }
        return $this->registry[$id];
    }

    /**
     * Retrieve by product id
     *
     * @param int $productId
     * @return ProductRecurringScheduleInterface
     */
    public function getByProductId($productId)
    {
        if (!isset($this->registryByProduct[$productId])) {
            /** @var ProductRecurringScheduleInterface $recurringSchedule */
            $recurringSchedule = $this->productRecurringScheduleFactory->create();
            $this->resource->load($recurringSchedule, $productId, ProductRecurringScheduleInterface::PRODUCT_ID);
            $this->registry[$recurringSchedule->getId()] = $recurringSchedule;
            $this->registryByProduct[$productId] = $recurringSchedule;
        }
        return $this->registryByProduct[$productId];
    }

    /**
     * Delete by product id
     *
     * @param int $productId
     */
    public function deleteByProductId($productId)
    {
        $this->resource->deleteByProductId($productId);
        unset($this->registryByProduct[$productId]);
    }
}
