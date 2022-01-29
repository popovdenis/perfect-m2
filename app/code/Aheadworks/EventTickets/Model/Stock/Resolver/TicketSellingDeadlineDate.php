<?php
namespace Aheadworks\EventTickets\Model\Stock\Resolver;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\TicketSellingDeadline as RecurringDeadline;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver\Recurring\TicketSaleDeadline;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class TicketSellingDeadlineDate
 *
 * @package Aheadworks\EventTickets\Model\Stock\Resolver
 */
class TicketSellingDeadlineDate
{
    /**
     * @var TicketSaleDeadline
     */
    private $recurringTicketSaleDeadline;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param TicketSaleDeadline $recurringTicketSaleDeadline
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        TicketSaleDeadline $recurringTicketSaleDeadline,
        TimezoneInterface $timezone
    ) {
        $this->recurringTicketSaleDeadline = $recurringTicketSaleDeadline;
        $this->timezone = $timezone;
    }

    /**
     * Resolve ticket selling deadline date
     *
     * @param Product $product
     * @param CartItemInterface|null $quoteItem
     * @return \DateTime
     */
    public function resolve($product, $quoteItem = null)
    {
        /** @var EventTicket $productType */
        $productType = $product->getTypeInstance();
        $timezone = $this->timezone->getConfigTimezone();

        switch ($productType->getTicketSellingDeadlineType($product)) {
            case TicketSellingDeadline::EVENT_END_DATE:
                $date = $productType->getEventEndDate($product);
                break;
            case TicketSellingDeadline::CUSTOM_DATE:
                $date = $productType->getTicketSellingDeadlineCustomDate($product);
                break;
            case RecurringDeadline::EVENT_START_DATE:
                $date = $this->recurringTicketSaleDeadline
                    ->getRecurringTicketSellingDeadlineEventStartDate($product, $quoteItem);
                return new \DateTime($date, new \DateTimeZone($timezone));
            case RecurringDeadline::IN_ADVANCE:
                $date = $this->recurringTicketSaleDeadline
                    ->getRecurringTicketSellingDeadlineInAdvance($product, $quoteItem);
                return new \DateTime($date, new \DateTimeZone($timezone));
            case TicketSellingDeadline::EVENT_START_DATE:
            default:
                $date = $productType->getEventStartDate($product);
        }

        $date = new \DateTime($date ?: 'now', new \DateTimeZone('UTC'));

        return $date->setTimezone(new \DateTimeZone($timezone));
    }
}
