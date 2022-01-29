<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class VenueId
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter
 */
class VenueId implements ConverterInterface
{
    /**
     * @var VenueRepositoryInterface
     */
    private $venueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param VenueRepositoryInterface $venueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        VenueRepositoryInterface $venueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->venueRepository = $venueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function convertRow($row)
    {
        $data = [];
        foreach ($row as $field => $value) {
            if ($field == 'venue') {
                $data[ProductAttributeInterface::CODE_AW_ET_VENUE_ID] = $this->getVenueIdByName($value);
            }
        }
        return $data;
    }

    /**
     * Retrieve venue id by name
     *
     * @param string $name
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getVenueIdByName($name)
    {
        $this->searchCriteriaBuilder
            ->addFilter(VenueInterface::NAME, $name)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $venues = $this->venueRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $firstVenue = reset($venues);

        return count($venues) > 0 ? $firstVenue->getId() : null;
    }
}
