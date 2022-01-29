<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver;

use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Model\Stock\Resolver\TicketSellingDeadlineDate as DeadlineDateResolver;

/**
 * Class TicketPrice
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver
 */
class TicketPrice
{
    /**
     * @var DeadlineDateResolver
     */
    private $deadlineDateResolver;

    /**
     * @param DeadlineDateResolver $deadlineDateResolver
     */
    public function __construct(DeadlineDateResolver $deadlineDateResolver)
    {
        $this->deadlineDateResolver = $deadlineDateResolver;
    }

    /**
     * Resolve ticket price
     *
     * @param Product $product
     * @param array $ticket
     * @return float
     */
    public function resolve($product, $ticket)
    {
        if ($product->getAwEtScheduleType() == ScheduleType::RECURRING) {
            return $ticket[ProductSectorTicketInterface::PRICE];
        }

        $eventStartDate = new \DateTime($product->getAwEtStartDate());
        $now = new \DateTime('now');

        $earlyBirdEndDate = $product->getAwEtEarlyBirdEndDate();
        $earlyBirdPrice = $ticket[ProductSectorTicketInterface::EARLY_BIRD_PRICE];
        if ($earlyBirdEndDate && isset($earlyBirdPrice)) {
            $earlyBirdEndDate = new \DateTime($earlyBirdEndDate);
            if ($now < $earlyBirdEndDate && $earlyBirdEndDate < $eventStartDate) {
                return $earlyBirdPrice;
            }
        }

        $lastDaysStartDate = $product->getAwEtLastDaysStartDate();
        $lastDaysPrice = $ticket[ProductSectorTicketInterface::LAST_DAYS_PRICE];
        if ($lastDaysStartDate && isset($lastDaysPrice)) {
            $lastDaysStartDate = new \DateTime($lastDaysStartDate);
            $deadlineDate = $this->deadlineDateResolver->resolve($product);
            if ($now > $lastDaysStartDate && $lastDaysStartDate < $deadlineDate) {
                return $lastDaysPrice;
            }
        }

        return $ticket[ProductSectorTicketInterface::PRICE];
    }
}
