<?php

namespace Perfect\Event\Model\Config\Source\Options;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Worker
 *
 * @package Perfect\Event\Model\Config\Source\Options
 */
class Worker implements OptionSourceInterface
{
    const MASTER_CUSTOMER_GROUP = 'Сотрудник';

    /**
     * @var \Perfect\Event\Helper\Customer
     */
    private $customerHelper;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var array
     */
    private $employees;

    /**
     * Worker constructor.
     *
     * @param \Perfect\Event\Helper\Customer                                   $customerHelper
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Perfect\Event\Helper\Customer $customerHelper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
    )
    {
        $this->customerHelper = $customerHelper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if($this->employees === null) {
            foreach ($this->getEmployees() as $employee) {
                $this->employees[] = ['value' => $employee->getId(), 'label' => $employee->getFirstname()];
            }
        }

        return $this->employees;
    }

    /**
     * @param array $optionsData
     *
     * @return array
     */
    public function getKeyValueArray(array $optionsData)
    {
        $options = [];
        foreach ($optionsData as $key => $value) {
            array_push($options, ['value' => $key, 'label' => $value]);
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getEmployees()
    {
        if ($employeeGroupId = $this->getEmployeeGroup()) {
            $customerCollection = $this->collectionFactory->create();
            $customerCollection->addFieldToFilter('group_id', ['eq' => $employeeGroupId]);

            return $customerCollection->getItems();
        }

        return [];
    }

    /**
     * @return int|null
     */
    protected function getEmployeeGroup()
    {
        try {
            /** @var \Magento\Customer\Api\Data\GroupInterface $workerGroup */
            $workerGroup = $this->customerHelper->getCustomerGroupByName(self::MASTER_CUSTOMER_GROUP);

            return $workerGroup->getId();
        } catch (LocalizedException $e) {
        }

        return null;
    }
}