<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options\Validator as OptionValidator;

/**
 * Class Validator
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest
 */
class Validator
{
    /**
     * @var OptionValidator
     */
    private $optionValidator;

    /**
     * @param OptionValidator $optionValidator
     */
    public function __construct(OptionValidator $optionValidator)
    {
        $this->optionValidator = $optionValidator;
    }

    /**
     * Validate buy request
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @param bool $isStrictProcessMode
     * @return void
     * @throws LocalizedException
     */
    public function validate($buyRequest, $product, $isStrictProcessMode)
    {
        if ($isStrictProcessMode) {
            $this->strictProcessModeValidate($buyRequest, $product);
        } else {
            $this->liteProcessModeValidate($buyRequest, $product);
        }
    }

    /**
     * Validate if product add to card
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return void
     * @throws LocalizedException
     */
    private function strictProcessModeValidate($buyRequest, $product)
    {
        $this
            ->validateTicketConfiguration($buyRequest)
            ->validateTicketBySector($buyRequest, $product)
            ->validateTicketPersonalOptions($buyRequest, $product);
    }

    /**
     * Validate if product add to wish list
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return void
     * @throws LocalizedException
     */
    private function liteProcessModeValidate($buyRequest, $product)
    {
        $this
            ->validateTicketConfiguration($buyRequest)
            ->validateTicketBySector($buyRequest, $product);
    }

    /**
     * Validate ticket configuration
     *
     * @param DataObject $buyRequest
     * @return $this
     * @throws LocalizedException
     */
    private function validateTicketConfiguration($buyRequest)
    {
        if (!empty($buyRequest->getAwEtTickets())) {
            return $this;
        }

        if (!empty($buyRequest->getAwEtSlots())) {
            return $this;
        }

        throw new LocalizedException(__('Please specify tickets.'));
    }

    /**
     * Validate tickets by sector
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return $this
     * @throws LocalizedException
     */
    private function validateTicketBySector($buyRequest, $product)
    {
        foreach ($buyRequest->getAwEtTickets() as $ticket) {
            $this->validateTicketDataBySector($ticket, $product);
        }

        foreach ($buyRequest->getAwEtSlots() as $slot) {
            foreach ($slot[OptionInterface::BUY_REQUEST_AW_ET_TICKETS] as $ticket) {
                $this->validateTicketDataBySector($ticket, $product);
            }
        }

        return $this;
    }

    /**
     * Validate ticket data by sector
     *
     * @param array $ticket
     * @param Product $product
     * @throws LocalizedException
     */
    private function validateTicketDataBySector($ticket, $product)
    {
        $sectorId = $ticket[OptionInterface::BUY_REQUEST_SECTOR_ID];
        $ticketTypeId = $ticket[OptionInterface::BUY_REQUEST_TYPE_ID];

        if (null === $product->getPriceModel()->getSelectionFinalPrice($product, $sectorId, $ticketTypeId)) {
            throw new LocalizedException(__('Incorrect ticket configuration.'));
        }
    }

    /**
     * Validate ticket personal options
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return $this
     * @throws LocalizedException
     */
    private function validateTicketPersonalOptions($buyRequest, $product)
    {
        /** @var ProductPersonalOptionInterface[] $options */
        $options = $product->getTypeInstance()->getPersonalOptions($product);
        if (empty($options)) {
            return $this;
        }

        foreach ($buyRequest->getAwEtTickets() as $ticket) {
            $this->validateTicketDataPersonalOptions($ticket, $options, $product);
        }

        foreach ($buyRequest->getAwEtSlots() as $slot) {
            foreach ($slot[OptionInterface::BUY_REQUEST_AW_ET_TICKETS] as $ticket) {
                $this->validateTicketDataPersonalOptions($ticket, $options, $product);
            }
        }

        return $this;
    }

    /**
     * Validate ticket data personal options
     *
     * @param array $ticket
     * @param array $options
     * @param Product $product
     * @throws LocalizedException
     */
    private function validateTicketDataPersonalOptions($ticket, $options, $product)
    {
        $attendees = $ticket[OptionInterface::BUY_REQUEST_ATTENDEE];
        $requiredFound = null;
        foreach ($options as $option) {
            $isOptionAvailable = $product->getTypeInstance()->isOptionAvailableForTicket(
                $option->getUid(),
                $product,
                $ticket[OptionInterface::BUY_REQUEST_SECTOR_ID],
                $ticket[OptionInterface::BUY_REQUEST_TYPE_ID]
            );
            if (!$isOptionAvailable) {
                continue;
            }
            $requiredFound = null === $requiredFound && $option->isRequire() ? : $requiredFound;
            foreach ($attendees as $attendee) {
                if (!$this->isValidOption($option, $attendee)) {
                    throw new LocalizedException(__('Please specify the required options.'));
                }
            }
        }
        if ($requiredFound && $ticket[OptionInterface::BUY_REQUEST_QTY] != count($attendees)) {
            throw new LocalizedException(__('Please specify the required options.'));
        }
    }

    /**
     * Check if valid option
     *
     * @param ProductPersonalOptionInterface $option
     * @param array $attendee
     * @return bool
     */
    private function isValidOption($option, $attendee)
    {
        $optName = $option->getId();
        $attendeeValue = isset($attendee[$optName]) ? $attendee[$optName] : '';

        return $this->optionValidator->setOption($option)->isValid($attendeeValue);
    }
}
