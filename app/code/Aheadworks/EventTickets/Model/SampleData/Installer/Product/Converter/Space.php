<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class SpaceId
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter
 */
class Space implements ConverterInterface
{
    /**
     * @var SpaceRepositoryInterface
     */
    private $spaceRepository;

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array|null
     */
    private $ticketTypePrices;

    /**
     * @var  array|null
     */
    private $ticketTypePositions;

    /**
     * @param SpaceRepositoryInterface $spaceRepository
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SpaceRepositoryInterface $spaceRepository,
        TicketTypeRepositoryInterface $ticketTypeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->spaceRepository = $spaceRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function convertRow($row)
    {
        $data = [];
        $space = $ticketTypes = null;
        foreach ($row as $field => $value) {
            if ($field == 'space') {
                $space = $this->getSpaceByName($value);
            }
            if ($field == 'ticket_type') {
                $ticketTypeNames = $this->extractMultipleOptions($value);
                $ticketTypes = $this->getTicketTypeByNames($ticketTypeNames);
            }
            if ($field == 'ticket_type_price') {
                $this->ticketTypePrices = $this->extractMultipleOptions($value);
            }
            if ($field == 'ticket_type_position') {
                $this->ticketTypePositions = $this->extractMultipleOptions($value);
            }
        }
        if ($space && $ticketTypes) {
            $data = [
                ProductAttributeInterface::CODE_AW_ET_SPACE_ID  => $space->getId(),
                'extension_attributes' => [
                    ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG =>
                        $this->prepareSectorConfig($space, $ticketTypes)
                ]
            ];
        }

        return $data;
    }

    /**
     * Retrieve space by name
     *
     * @param string $name
     * @return SpaceInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSpaceByName($name)
    {
        $this->searchCriteriaBuilder
            ->addFilter(SpaceInterface::NAME, $name)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $spaces = $this->spaceRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($spaces) > 0 ? reset($spaces) : null;
    }

    /**
     * Retrieve ticket types by name
     *
     * @param string[] $names
     * @return TicketTypeInterface[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTicketTypeByNames($names)
    {
        $this->searchCriteriaBuilder->addFilter(SpaceInterface::NAME, $names, 'in');
        $ticketTypes = $this->ticketTypeRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($ticketTypes) > 0 ? $ticketTypes : null;
    }

    /**
     * Prepare sector config
     *
     * @param SpaceInterface $space
     * @param TicketTypeInterface[] $ticketTypes
     * @return array
     */
    private function prepareSectorConfig($space, $ticketTypes)
    {
        $prepared = [];
        $spaceSectors = $space->getSectors();
        foreach ($spaceSectors as $sector) {
            $productSectorData = [ProductSectorInterface::SECTOR_ID => $sector->getId()];
            foreach ($ticketTypes as $ticketTypeKey => $ticket) {
                $productSectorData[ProductSectorInterface::SECTOR_TICKETS][] = [
                    ProductSectorTicketInterface::TYPE_ID  => $ticket->getId(),
                    ProductSectorTicketInterface::PRICE    => $this->resolveTicketTypePrice($ticketTypeKey),
                    ProductSectorTicketInterface::POSITION => $this->resolveTicketTypePosition($ticketTypeKey),
                    ProductSectorTicketInterface::PERSONAL_OPTION_UIDS => []
                ];
            }
            $prepared[] = $productSectorData;
        }

        return $prepared;
    }

    /**
     * Resolve ticket type price
     *
     * @param int $ticketTypeKey
     * @return int
     */
    private function resolveTicketTypePrice($ticketTypeKey)
    {
        if (isset($this->ticketTypePrices[$ticketTypeKey])) {
            return $this->ticketTypePrices[$ticketTypeKey];
        }
        return 0;
    }

    /**
     * Resolve ticket type position
     *
     * @param int $ticketTypeKey
     * @return int
     */
    private function resolveTicketTypePosition($ticketTypeKey)
    {
        if (isset($this->ticketTypePrices[$ticketTypeKey])) {
            return $this->ticketTypePrices[$ticketTypeKey];
        }
        return 0;
    }

    /**
     * Extract multiple options from string
     *
     * @param string $value
     * @return string[]
     */
    private function extractMultipleOptions($value)
    {
        $preparedValues = [];
        $values = explode(',', $value);
        foreach ($values as $value) {
            if (!empty($value)) {
                $preparedValues[] = $value;
            }
        }

        return $preparedValues;
    }
}
