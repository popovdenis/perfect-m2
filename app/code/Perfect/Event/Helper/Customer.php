<?php

namespace Perfect\Event\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\Context;

/**
 * Class Customer
 *
 * @package Perfect\Event\Helper
 */
class Customer extends AbstractHelper
{
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Customer constructor.
     *
     * @param \Magento\Framework\App\Helper\Context          $context
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder   $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct($context);
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param $groupName
     *
     * @return \Magento\Customer\Api\Data\GroupInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroupByName($groupName)
    {
        $this->searchCriteriaBuilder->addFilter('customer_group_code', $groupName);

        $items = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return $items ? array_shift($items) : null;
    }
}