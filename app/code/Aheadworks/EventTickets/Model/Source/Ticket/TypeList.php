<?php
namespace Aheadworks\EventTickets\Model\Source\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Convert\DataObject as DataObjectConverter;

/**
 * Class TypeList
 *
 * @package Aheadworks\EventTickets\Model\Source\Ticket
 */
class TypeList implements OptionSourceInterface
{
    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var array
     */
    private $options;

    /**
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param DataObjectConverter $dataObjectConverter
     */
    public function __construct(
        TicketTypeRepositoryInterface $ticketTypeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        DataObjectConverter $dataObjectConverter
    ) {
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $ticketTypesArray = $this->getTicketTypesArray();
            $this->options = $this->getOptionsFromTicketTypesArray($ticketTypesArray);
        }

        return $this->options;
    }

    /**
     * Retrieve tickets types for generating options
     *
     * @return TicketTypeInterface[]|array
     */
    private function getTicketTypesArray()
    {
        $ticketTypes = [];
        try {
            $sortOrder = $this->sortOrderBuilder
                ->setField(TicketTypeInterface::ID)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(TicketTypeInterface::STATUS, Status::STATUS_ENABLED)
                ->addSortOrder($sortOrder)
                ->create();
            $ticketTypes = $this->ticketTypeRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $exception) {
        }

        return $ticketTypes;
    }

    /**
     * Retrieve options array from the array of ticket types
     *
     * @param TicketTypeInterface[] $ticketTypesArray
     * @return array
     */
    private function getOptionsFromTicketTypesArray($ticketTypesArray)
    {
        $callable = function (TicketTypeInterface $item) {
            $optionLabel = '';
            $currentLabels = $item->getCurrentLabels();
            if ($currentLabels instanceof StorefrontLabelsInterface) {
                $optionLabel = $currentLabels->getTitle();
            }
            return $optionLabel;
        };

        return $this->dataObjectConverter->toOptionArray($ticketTypesArray, TicketTypeInterface::ID, $callable);
    }
}
