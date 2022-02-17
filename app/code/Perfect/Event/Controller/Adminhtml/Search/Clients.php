<?php

namespace Perfect\Event\Controller\Adminhtml\Search;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Clients
 *
 * @package Perfect\Event\Controller\Adminhtml\Search
 */
class Clients extends \Magento\Backend\App\Action
{
    const CLIENT_CUSTOMER_GROUP = 'Клиент';

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Perfect\Event\Helper\Customer
     */
    private $customerHelper;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollection;

    /**
     * Clients constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Perfect\Event\Helper\Customer               $customerHelper
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
        $this->customerHelper = $customerHelper;
        $this->customerCollection = $customerCollection;
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
        if ($clients = $this->getClientsBySearch($search)) {
            /** @var CustomerInterface $customer */
            foreach ($clients as $customer) {
                $results[] = [
                    'client_id' => $customer->getId(),
                    'client' => sprintf('%s %s', $customer->getFirstname(), $customer->getLastname()),
                    'client_phone' => $customer->getPhone()
                ];
            }
        }

        return $this->goBack($results);
    }

    /**
     * @param                                                    $search
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getClientsBySearch($search)
    {
        if ($customerGroupId = $this->getClientGroup()) {
            $customerCollection = $this->customerCollection->create();
            $customerCollection->addFieldToFilter('firstname', ['like' => '%' . $search . '%']);
            $customerCollection->addFieldToFilter('group_id', ['eq' => $this->getClientGroup()]);
            $customerCollection->addAttributeToSelect('phone');

            return $customerCollection->getItems();
        }

        return [];
    }

    /**
     * @return int|null
     */
    protected function getClientGroup()
    {
        try {
            /** @var \Magento\Customer\Api\Data\GroupInterface $workerGroup */
            $workerGroup = $this->customerHelper->getCustomerGroupByName(self::CLIENT_CUSTOMER_GROUP);

            return $workerGroup->getId();
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