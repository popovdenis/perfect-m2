<?php

namespace Perfect\Service\Controller\Adminhtml\Master;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Service
 *
 * @package Perfect\Service\Controller\Adminhtml\Master
 */
class Service extends \Magento\Backend\App\Action
{
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
     * Service constructor.
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
        $masterId = (int) $this->getRequest()->getParam('masterId');

        if (empty($masterId)) {
            return $this->goBack([]);
        }

        $results = [];
        if ($services = $this->getServicesByMaster($masterId)) {
            /** @var CustomerInterface $customer */
            foreach ($services as $customer) {
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
     * @param   $search
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getServicesByMaster($masterId)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addFieldToFilter('firstname', ['like' => '%' . $search . '%']);
        $customerCollection->addFieldToFilter('group_id', ['eq' => $this->getClientGroup()]);
        $customerCollection->addAttributeToSelect('phone');

        return $customerCollection->getItems();
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