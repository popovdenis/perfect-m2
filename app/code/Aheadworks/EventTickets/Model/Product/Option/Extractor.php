<?php
namespace Aheadworks\EventTickets\Model\Product\Option;

use Aheadworks\EventTickets\Api\Data\AttendeeInterface;
use Aheadworks\EventTickets\Api\Data\AttendeeInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\OptionInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class Extractor
 *
 * @package Aheadworks\EventTickets\Model\Product\Option
 */
class Extractor
{
    /**
     * Item attendee options prefix
     */
    const OPTION_ATTENDEE_PREFIX = 'aw_et_opt_attendee_';

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var AttendeeInterfaceFactory
     */
    private $attendeeFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param OptionInterfaceFactory $optionFactory
     * @param AttendeeInterfaceFactory $attendeeFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        OptionInterfaceFactory $optionFactory,
        AttendeeInterfaceFactory $attendeeFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->optionFactory = $optionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->attendeeFactory = $attendeeFactory;
    }

    /**
     * Extract event ticket options from product option
     *
     * @param array $options
     * @param Product $product
     * @return OptionInterface
     */
    public function extractFromArray(array $options, $product = null)
    {
        /** @var OptionInterface $optionObject */
        $optionsObject = $this->optionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $optionsObject,
            $options,
            OptionInterface::class
        );

        if ($product && $this->isAttachAttendeeOptions($optionsObject)) {
            $attendees = $this->extractAttendeeFromArray(
                $optionsObject->getAwEtAttendeeIds(),
                $optionsObject->getAwEtOptionAttendeeIds(),
                $product,
                $options
            );
            $optionsObject->setAwEtAttendees($attendees);
        }

        return $optionsObject;
    }

    /**
     * Extract event ticket options from object
     *
     * @param OptionInterface $options
     * @return array
     */
    public function extractFromObject(OptionInterface $options)
    {
        return $this->dataObjectProcessor->buildOutputDataArray($options, OptionInterface::class);
    }

    /**
     * Compose attendee option name
     *
     * @param int $optionId
     * @param int $attendeeId
     * @return string
     */
    public function composeAttendeeOptionName($optionId, $attendeeId)
    {
        return self::OPTION_ATTENDEE_PREFIX . $attendeeId . '_' . $optionId;
    }

    /**
     * Compose option ids
     *
     * @param array $ids
     * @return string
     */
    public function composeOptionIds($ids)
    {
        return implode(',', $ids);
    }

    /**
     * Extract option ids
     *
     * @param string $ids
     * @return array
     */
    public function extractOptionIds($ids)
    {
        return explode(',', $ids);
    }

    /**
     * Check if attach attendee options
     *
     * @param OptionInterface $options
     * @return bool
     */
    private function isAttachAttendeeOptions($options)
    {
        return null !== $options->getAwEtAttendeeIds() && null !== $options->getAwEtOptionAttendeeIds();
    }

    /**
     * Prepare attendee options
     *
     * @param string $attendeeIds
     * @param string $optionAttendeeIds
     * @param Product $product
     * @param array $options
     * @return AttendeeInterface[]
     */
    private function extractAttendeeFromArray($attendeeIds, $optionAttendeeIds, $product, $options)
    {
        /** @var ProductPersonalOptionInterface[] $productOptions */
        $productOptions = $product->getTypeInstance()->getPersonalOptions($product);
        $optionAttendeeIds = $this->extractOptionIds($optionAttendeeIds);
        $attendeeIds = $this->extractOptionIds($attendeeIds);
        if (empty($productOptions) || empty($optionAttendeeIds) || empty($attendeeIds)) {
            return [];
        }

        $attendees = [];
        foreach ($attendeeIds as $attendeeId) {
            foreach ($optionAttendeeIds as $optionAttendeeId) {
                /** @var ProductPersonalOptionInterface $option */
                if ($option = $product->getTypeInstance()->getOptionById($product, $optionAttendeeId)) {
                    /** @var AttendeeInterface $attendee */
                    $attendee = $this->attendeeFactory->create();
                    $attendee
                        ->setAttendeeId((int)$attendeeId + 1)
                        ->setProductOption($option)
                        ->setLabel($option->getCurrentLabels()->getTitle())
                        ->setValue($this->resolveAttendeeOptionValue($option, $attendeeId, $options));

                    $attendees[] = $attendee;
                }
            }
        }

        return $attendees;
    }

    /**
     * Resolve attendee option value
     *
     * @param ProductPersonalOptionInterface $option
     * @param int $attendeeId
     * @param array $options
     * @return string
     */
    private function resolveAttendeeOptionValue($option, $attendeeId, $options)
    {
        $optName = $this->composeAttendeeOptionName($option->getId(), $attendeeId);
        $value = isset($options[$optName]) ? $options[$optName] : '';
        if (empty($value) || empty($option->getValues())) {
            return $value;
        }

        foreach ($option->getValues() as $optionValue) {
            if ($optionValue->getId() == $value) {
                return $optionValue->getCurrentLabels()->getTitle();
            }
        }

        return $value;
    }
}
