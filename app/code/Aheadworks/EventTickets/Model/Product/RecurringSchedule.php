<?php
namespace Aheadworks\EventTickets\Model\Product;

use Aheadworks\EventTickets\Api\Data\ScheduleOptionInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\ObjectDataProcessor;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator\Composite;
use Aheadworks\EventTickets\Model\ResourceModel\Product\RecurringSchedule as RecurringScheduleResource;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class RecurringSchedule
 * @package Aheadworks\EventTickets\Model\Product
 */
class RecurringSchedule extends AbstractModel implements ProductRecurringScheduleInterface
{
    /**
     * @var ObjectDataProcessor
     */
    private $objectDataProcessor;

    /**
     * @var Composite
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ObjectDataProcessor $objectDataProcessor
     * @param Composite $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ObjectDataProcessor $objectDataProcessor,
        Composite $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->objectDataProcessor = $objectDataProcessor;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RecurringScheduleResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellingDeadlineType()
    {
        return $this->getData(self::SELLING_DEADLINE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellingDeadlineType($type)
    {
        return $this->setData(self::SELLING_DEADLINE_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellingDeadlineCorrection()
    {
        return $this->getData(self::SELLING_DEADLINE_CORRECTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellingDeadlineCorrection($deadlineCorrection)
    {
        return $this->setData(self::SELLING_DEADLINE_CORRECTION, $deadlineCorrection);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduleOptions()
    {
        return $this->getData(self::SCHEDULE_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduleOptions($options)
    {
        return $this->setData(self::SCHEDULE_OPTIONS, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeSlots()
    {
        return $this->getData(self::TIME_SLOTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeSlots($timeSlots)
    {
        return $this->setData(self::TIME_SLOTS, $timeSlots);
    }

    /**
     * @inheritDoc
     */
    public function getDaysToDisplay()
    {
        return $this->getData(self::DAYS_TO_DISPLAY);
    }

    /**
     * @inheritDoc
     */
    public function setDaysToDisplay($daysToDisplay)
    {
        return $this->setData(self::DAYS_TO_DISPLAY, $daysToDisplay);
    }

    /**
     * @inheritDoc
     */
    public function getFilterByTicketQty()
    {
        return $this->getData(self::FILTER_BY_TICKET_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setFilterByTicketQty($filterByTicketQty)
    {
        return $this->setData(self::FILTER_BY_TICKET_QTY, $filterByTicketQty);
    }

    /**
     * @inheritDoc
     */
    public function getMultiselectionTimeSlots()
    {
        return $this->getData(self::MULTISELECTION_TIME_SLOTS);
    }

    /**
     * @inheritDoc
     */
    public function setMultiselectionTimeSlots($multiselectionTimeSlots)
    {
        return $this->setData(self::MULTISELECTION_TIME_SLOTS, $multiselectionTimeSlots);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->objectDataProcessor->prepareDataBeforeSave($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoad()
    {
        $this->objectDataProcessor->prepareDataAfterLoad($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * Retrieve option by key
     *
     * @param string $key
     * @return ScheduleOptionInterface
     */
    public function getOptionByKey($key)
    {
        foreach ($this->getScheduleOptions() as $option) {
            if ($option->getKey() == $key) {
                return $option;
            }
        }

        return null;
    }
}
