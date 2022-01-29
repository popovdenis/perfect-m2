<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver\Recurring;

use Aheadworks\EventTickets\Api\Data\DeadlineCorrectionInterface;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\DateTime;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class TicketSaleDeadline
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver\Recurring
 */
class TicketSaleDeadline
{
    /**
     * Get Recurring Ticket Selling Deadline Event Start Date
     *
     * @param Product $product
     * @param CartItemInterface|null $quoteItem
     * @return string
     */
    public function getRecurringTicketSellingDeadlineEventStartDate($product, $quoteItem = null)
    {
        $date = new \DateTime();

        if (!$quoteItem) {
            $startDate = $date->modify('+1 year');
        } else {
            $startDate = $this->prepareStartDate($product, $quoteItem);
            $startDate = $startDate ? new \DateTime($startDate) : $date->modify('-1 year');
        }

        return $startDate->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * Get Recurring Ticket Selling Deadline In Advance
     *
     * @param Product $product
     * @param CartItemInterface|null $quoteItem
     * @return string
     */
    public function getRecurringTicketSellingDeadlineInAdvance($product, $quoteItem = null)
    {
        /** @var ProductRecurringScheduleInterface $recurringSchedule */
        $recurringSchedule = $product->getExtensionAttributes()->getAwEtRecurringSchedule();
        $date = new \DateTime();

        if (!$quoteItem) {
            return $date->modify('+1 year')->format(DateTime::DATETIME_PHP_FORMAT);
        }

        $startDate = $this->prepareStartDate($product, $quoteItem);
        if (!$startDate) {
            return $date->modify('-1 year')->format(DateTime::DATETIME_PHP_FORMAT);
        }

        try {
            $date = new \DateTime($startDate);
        } catch (\Exception $e) {
            return $date->modify('-1 year')->format(DateTime::DATETIME_PHP_FORMAT);
        }
        $deadlineCorrections = $recurringSchedule->getSellingDeadlineCorrection();
        $date = $date->modify($this->prepareModifyString($deadlineCorrections));

        return $date->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * Prepare start date for recurring event
     *
     * @param Product $product
     * @param CartItemInterface $quoteItem
     * @return string|null
     */
    private function prepareStartDate($product, $quoteItem)
    {
        /** @var ProductRecurringScheduleInterface $recurringSchedule */
        $recurringSchedule = $product->getExtensionAttributes()->getAwEtRecurringSchedule();

        $startDateOption = $quoteItem->getProduct()->getCustomOption(OptionInterface::RECURRING_START_DATE);
        if (!$startDateOption && !$recurringSchedule) {
            return null;
        }

        return $startDateOption->getValue();
    }

    /**
     * Prepare string for modify date
     *
     * @param DeadlineCorrectionInterface $deadlineCorrections
     * @return string
     */
    private function prepareModifyString($deadlineCorrections)
    {
        return sprintf(
            '-%d day -%d hours -%d minutes',
            $deadlineCorrections->getDays(),
            $deadlineCorrections->getHours(),
            $deadlineCorrections->getMinutes()
        );
    }
}
