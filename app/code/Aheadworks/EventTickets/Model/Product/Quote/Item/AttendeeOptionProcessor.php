<?php
namespace Aheadworks\EventTickets\Model\Product\Quote\Item;

use Aheadworks\EventTickets\Model\Product\Quote\Item\AttendeeOptionProcessor\Resolver\AttendeeOptionsToAdd
    as AttendeeOptionsToAddResolver;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;

/**
 * Class AttendeeOptionProcessor
 *
 * @package Aheadworks\EventTickets\Model\Product\Quote\Item
 */
class AttendeeOptionProcessor
{
    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var AttendeeOptionsToAddResolver
     */
    private $attendeeOptionsToAddResolver;

    /**
     * @param OptionExtractor $optionExtractor
     * @param AttendeeOptionsToAddResolver $attendeeOptionsToAddResolver
     */
    public function __construct(
        OptionExtractor $optionExtractor,
        AttendeeOptionsToAddResolver $attendeeOptionsToAddResolver
    ) {
        $this->optionExtractor = $optionExtractor;
        $this->attendeeOptionsToAddResolver = $attendeeOptionsToAddResolver;
    }

    /**
     * Process attendee options
     *
     * @param Item $item
     * @param bool $isSetEmptyOptions
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processAttendeeOptions(&$item, $isSetEmptyOptions = true)
    {
        $attendeeOptions = $this->attendeeOptionsToAddResolver->resolve($item, $isSetEmptyOptions);
        $attendeeIds = $this->extractAttendeeIds($item);

        if (!empty($attendeeOptions)) {
            $itemQty = $item->getQty();
            $productAttendeeQty = count($attendeeIds);
            $qty = abs($itemQty - $productAttendeeQty);

            if ($itemQty > $productAttendeeQty) {
                $attendeeIds = $this->addAttendeeOptionsInQty($qty, $item, $attendeeOptions, $attendeeIds);
            } elseif ($itemQty < $productAttendeeQty) {
                $attendeeIds = $this->removeAttendeeOptionsInQty($qty, $item, $attendeeOptions, $attendeeIds);
            }

            $optionIds = $this->composeAttendeeOptionIds($attendeeOptions);
            $this
                ->updateOptionIds($item, OptionInterface::OPTION_ATTENDEE_IDS, $optionIds)
                ->updateOptionIds($item, OptionInterface::ATTENDEE_IDS, array_keys($attendeeIds));
        }
    }

    /**
     * Remove attendee options in qty
     *
     * @param int $qty
     * @param Item $item
     * @param DataObject[] $attendeeOptions
     * @param array $attendeeIds
     * @return array
     */
    private function removeAttendeeOptionsInQty($qty, $item, $attendeeOptions, $attendeeIds)
    {
        for ($i = 1; $i <= $qty; $i++) {
            $attendeeNumber = count($attendeeIds) - 1;
            foreach ($attendeeOptions as $option) {
                $optName = $this->optionExtractor->composeAttendeeOptionName($option->getId(), $attendeeNumber);
                $item->removeOption($optName);
            }
            array_pop($attendeeIds);
        }
        return $attendeeIds;
    }

    /**
     * Add attendee options in qty
     *
     * @param int $qty
     * @param Item $item
     * @param DataObject[] $attendeeOptions
     * @param array $attendeeIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addAttendeeOptionsInQty($qty, $item, $attendeeOptions, $attendeeIds)
    {
        for ($i = 1; $i <= $qty; $i++) {
            $attendeeNumber = count($attendeeIds);
            foreach ($attendeeOptions as $option) {
                $optName = $this->optionExtractor->composeAttendeeOptionName($option->getId(), $attendeeNumber);
                $optVal = $option->getValue();

                $item->addOption([
                    'code' => $optName,
                    'value' => $optVal,
                    'product_id' => $item->getProduct()->getId()
                ]);
            }
            array_push($attendeeIds, $attendeeNumber + 1);
        }
        return $attendeeIds;
    }

    /**
     * Update ids option by code
     *
     * @param Item $item
     * @param string $optionCode
     * @param array $ids
     * @return $this
     */
    private function updateOptionIds($item, $optionCode, $ids)
    {
        $option = $item->getOptionByCode($optionCode);
        $option->setValue($this->optionExtractor->composeOptionIds($ids));

        return $this;
    }

    /**
     * Compose attendee option ids
     *
     * @param DataObject[] $attendeeOptions
     * @return array
     */
    private function composeAttendeeOptionIds($attendeeOptions)
    {
        $optionIds = [];
        foreach ($attendeeOptions as $option) {
            $optionIds[] = $option->getId();
        }

        return $optionIds;
    }

    /**
     * Retrieve attendee ids option value
     *
     * @param Item $item
     * @return array
     */
    private function extractAttendeeIds($item)
    {
        $attendeeIdsOption = $item->getOptionByCode(OptionInterface::ATTENDEE_IDS);
        $attendeeIds = is_object($attendeeIdsOption)
            ? $this->optionExtractor->extractOptionIds($attendeeIdsOption->getValue())
            : [];

        return $attendeeIds;
    }
}
