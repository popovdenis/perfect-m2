<?php

namespace Perfect\Service\Ui\Component\Service\Form\Employers;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Options
 *
 * @package Perfect\Service\Ui\Component\Service\Form\Employers
 */
class Options implements OptionSourceInterface
{
    const MASTER_CUSTOMER_GROUP = 'Сотрудник';

    /**
     * @var array
     */
    protected $employers;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Perfect\Event\Helper\Customer
     */
    private $customerHelper;

    /**
     * Options constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Perfect\Event\Helper\Customer $customerHelper
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->customerHelper = $customerHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getEmployees();
    }

    /**
     * @return array
     */
    protected function getEmployees()
    {
        if ($this->employers === null) {
            if ($customerGroupId = $this->getEmployeeGroup()) {
                $customerCollection = $this->collectionFactory->create();
                $customerCollection->addFieldToFilter('group_id', ['eq' => $customerGroupId]);

                $this->employers[] = ['value' => '', 'label' => __('Выберите сотрудника')];
                foreach ($customerCollection->getItems() as $customer) {
                    $this->employers[] = [
                        'value' => $customer->getId(),
                        'label' => $customer->getFirstname()
                    ];
                }
            }
        }

        return $this->employers;
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