<?php

namespace Perfect\Event\Controller\Adminhtml\Timetable;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Search
 *
 * @package Perfect\Event\Controller\Adminhtml\Timetable
 */
class Search extends \Magento\Backend\App\Action
{
    const CLIENT_CUSTOMER_GROUP = 'Клиент';
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollection;
    /**
     * @var \Perfect\Event\Helper\Customer
     */
    private $customerHelper;

    /**
     * Search constructor.
     *
     * @param \Magento\Backend\App\Action\Context                              $context
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                     $searchCriteriaBuilder
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param \Perfect\Event\Helper\Customer                                   $customerHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Perfect\Event\Helper\Customer $customerHelper
    )
    {
        parent::__construct($context);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerCollection = $customerCollection;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Execute method.
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $search = $this->getRequest()->getParam('search');

        if (empty($search)) {
            return $this->goBack([]);
        }

        $results = [];
        if ($customers = $this->getCustomersBySearch($search)) {
            foreach ($customers as $customer) {
                $results[] = [
                    'firstname' => $customer->getFirstname(),
                    'phone' => $customer->getPhone(),
                    'email' => $customer->getEmail(),
                ];
            }
        }

        return $this->goBack($results);
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    protected function getCustomersBySearch($search)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addFieldToFilter('firstname', ['like' => '%' . $search . '%']);
        $customerCollection->addFieldToFilter('group_id', ['eq' => $this->getClientGroup()]);

        return $customerCollection->getItems();
    }

    /**
     * @return int|null
     */
    protected function getClientGroup()
    {
        try {
            /** @var \Magento\Customer\Api\Data\GroupInterface $clientGroup */
            $clientGroup = $this->customerHelper->getCustomerGroupByName(self::CLIENT_CUSTOMER_GROUP);

            return $clientGroup->getId();
        } catch (LocalizedException $e) {
        }

        return null;
    }

    /**
     * @param array $providers
     *
     * @return string
     */
    private function goBack($providers)
    {
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($providers);
    }
}