<?php
namespace Aheadworks\EventTickets\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;

/**
 * Class Groups
 *
 * @package Aheadworks\EventTickets\Model\Source
 */
class Groups implements OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectConverter = $objectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        return $this->objectConverter->toOptionArray($customerGroups, 'id', 'code');
    }
}
