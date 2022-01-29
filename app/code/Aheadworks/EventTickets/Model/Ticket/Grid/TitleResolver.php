<?php
namespace Aheadworks\EventTickets\Model\Ticket\Grid;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class TitleResolver
 * @package Aheadworks\EventTickets\Model\Ticket\Grid
 */
class TitleResolver
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatter;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param TimezoneInterface $timezone
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param RequestInterface $request
     */
    public function __construct(
        TimezoneInterface $timezone,
        DateTimeFormatterInterface $dateTimeFormatter,
        RequestInterface $request
    ) {
        $this->timezone = $timezone;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->request = $request;
    }

    /**
     * Retrieve product description for tickets grid title
     *
     * @param ProductInterface $eventProduct
     * @return string
     */
    public function getEventProductDescription($eventProduct)
    {
        switch ($eventProduct->getAwEtScheduleType()) {
            case ScheduleType::ONE_TIME:
                $productDescription = $this->getOneTimeProductDescription($eventProduct);
                break;
            case ScheduleType::RECURRING:
                $productDescription = $this->getRecurringProductDescription($eventProduct);
                break;
            default:
                $productDescription = '';
                break;
        }

        return $productDescription;
    }

    /**
     * Retrieve one time event description
     *
     * @param ProductInterface $eventProduct
     * @return string
     */
    private function getOneTimeProductDescription($eventProduct)
    {
        $name = $eventProduct->getName();
        $startDate = new \DateTime($eventProduct->getAwEtStartDate());
        $startDate = $this->dateTimeFormatter->formatObject(
            $startDate,
            $this->timezone->getDateTimeFormat(\IntlDateFormatter::MEDIUM)
        );

        return sprintf("%s - %s", $name, $startDate);
    }

    /**
     * Retrieve recurring event description
     *
     * @param ProductInterface $eventProduct
     * @return string
     */
    private function getRecurringProductDescription($eventProduct)
    {
        $productDescription = '';
        $name = $eventProduct->getName();
        $timeSlotId = (int)$this->request->getParam('slot_id');
        $eventDate = (string)$this->request->getParam('event_date');
        /** @var ProductRecurringScheduleInterface $recurringSchedule */
        $recurringSchedule = $eventProduct->getExtensionAttributes()
            ? $eventProduct->getExtensionAttributes()->getAwEtRecurringSchedule()
            : null;

        if ($recurringSchedule && $eventDate && $timeSlotId) {
            foreach ($recurringSchedule->getTimeSlots() as $timeSlot) {
                if ($timeSlot->getId() == $timeSlotId) {
                    $startTime = new \DateTime($timeSlot->getStartTime());
                    $endTime = new \DateTime($timeSlot->getEndTime());
                    $eventDate = new \DateTime($eventDate);
                    $productDescription = $productDescription = sprintf(
                        "%s - %s | %s - %s",
                        $name,
                        $this->dateTimeFormatter->formatObject(
                            $eventDate,
                            $this->timezone->getDateFormat(\IntlDateFormatter::MEDIUM)
                        ),
                        $this->dateTimeFormatter->formatObject($startTime, $this->timezone->getTimeFormat()),
                        $this->dateTimeFormatter->formatObject($endTime, $this->timezone->getTimeFormat())
                    );
                    break;
                }
            }
        }

        return $productDescription;
    }
}
