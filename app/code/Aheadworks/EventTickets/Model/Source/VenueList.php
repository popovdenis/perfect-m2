<?php
namespace Aheadworks\EventTickets\Model\Source;

use Aheadworks\EventTickets\Model\ResourceModel\Venue\Collection;
use Aheadworks\EventTickets\Model\ResourceModel\Venue\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class VenueList
 *
 * @package Aheadworks\EventTickets\Model\Source
 */
class VenueList implements OptionSourceInterface
{
    /**
     * Any venue value
     */
    const ANY_VENUE = 0;

    /**
     * @var Collection
     */
    private $venueCollection;

    /**
     * @var array
     */
    private $options;

    /**
     * @param CollectionFactory $venueCollectionFactory
     */
    public function __construct(
        CollectionFactory $venueCollectionFactory
    ) {
        $this->venueCollection = $venueCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = array_merge(
                [
                    ['label' => __('Any Venue'), 'value' => self::ANY_VENUE]
                ],
                $this->venueCollection->toOptionArray()
            );
        }

        return $this->options;
    }
}
