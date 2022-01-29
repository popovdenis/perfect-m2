<?php
namespace Aheadworks\EventTickets\Model\Product\Quote\Item\AttendeeOptionProcessor\Resolver;

use Aheadworks\EventTickets\Api\Data\AttendeeInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote\Item;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;

/**
 * Class AttendeeOptionsToAdd
 *
 * @package Aheadworks\EventTickets\Model\Product\Quote\Item\AttendeeOptionProcessor\Resolver
 */
class AttendeeOptionsToAdd
{
    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param OptionExtractor $optionExtractor
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        OptionExtractor $optionExtractor,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->optionExtractor = $optionExtractor;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Resolve attendee options by $isSetEmptyOptions flag
     *
     * @param Item $item
     * @param bool $isSetEmptyOptions
     * @return DataObject[]
     */
    public function resolve($item, $isSetEmptyOptions)
    {
        $product = $item->getProduct();
        if ($isSetEmptyOptions) {
            $attendeeOptions = $this->resolveOptionsAsEmpty($product);
        } else {
            $attendeeOptions = $this->resolveOptionsAsLastOption($item, $product);
        }

        return $attendeeOptions;
    }

    /**
     * Resolve options as empty
     *
     * @param Product $product
     * @return DataObject[]
     */
    private function resolveOptionsAsEmpty($product)
    {
        $resolvedAttendeeOptions = [];
        /** @var ProductPersonalOptionInterface[] $attendeeOptions */
        $attendeeOptions = $product->getTypeInstance()->getPersonalOptions($product, false);
        foreach ($attendeeOptions as $attendeeOption) {
            $resolvedAttendeeOptions[] = $this->createOption($attendeeOption->getId(), '');
        }

        return $resolvedAttendeeOptions;
    }

    /**
     * Resolve last item options
     *
     * @param Item $item
     * @param Product $product
     * @return DataObject[]
     */
    private function resolveOptionsAsLastOption($item, $product)
    {
        $resolvedAttendeeOptions = [];
        $options = $this->getQuoteItemOptions($item);
        $objectOptions = $this->optionExtractor->extractFromArray($options, $product);
        /** @var AttendeeInterface[] $attendeeOptions */
        $attendeeOptions = $objectOptions->getAwEtAttendees() ? : [];
        $attendeeIds = $this->optionExtractor->extractOptionIds($objectOptions->getAwEtAttendeeIds());
        $lastAttendeeId = (int)end($attendeeIds) + 1;
        foreach ($attendeeOptions as $attendeeOption) {
            if ($attendeeOption->getAttendeeId() != $lastAttendeeId) {
                continue;
            }
            $resolvedAttendeeOptions[] = $this->createOption(
                $attendeeOption->getProductOption()->getId(),
                $attendeeOption->getValue()
            );
        }

        return $resolvedAttendeeOptions;
    }

    /**
     * Retrieve quote item options
     *
     * @param Item $item
     * @return array
     */
    private function getQuoteItemOptions($item)
    {
        $options = [];
        foreach ($item->getOptions() as $itemOption) {
            $options[$itemOption->getCode()] = $itemOption->getValue();
        }
        return $options;
    }

    /**
     * Create option object
     *
     * @param int $id
     * @param mixed $value
     * @return DataObject
     */
    private function createOption($id, $value)
    {
        return $this->dataObjectFactory->create(['data' => ['id' => $id, 'value' => $value]]);
    }
}
