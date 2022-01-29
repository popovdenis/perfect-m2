<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Product;

use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\FormatterInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOptionRepository;
use Aheadworks\EventTickets\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Class PersonalOptionUid
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\Product
 */
class PersonalOptionUid implements FormatterInterface
{
    /**
     * @var PersonalOptionRepository
     */
    private $personalOptionRepository;

    /**
     * @param PersonalOptionRepository $personalOptionRepository
     */
    public function __construct(
        PersonalOptionRepository $personalOptionRepository
    ) {
        $this->personalOptionRepository = $personalOptionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        try {
            $personalOption = $this->personalOptionRepository->getByUid($value, StoreOptions::ALL_STORE_VIEWS);
            $formattedValue = $personalOption->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
            $formattedValue = '';
        }
        return $formattedValue;
    }
}
