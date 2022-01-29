<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring;

use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface;
use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\LayoutProcessorInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Ticket\AvailableTicket\Resolver as AvailableTicketResolver;
use Aheadworks\EventTickets\Model\Product\Sector;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\TicketSellingDeadline;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\Store;

/**
 * Class Provider
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring
 */
class Provider implements LayoutProcessorInterface
{
    /**#@+
     * Calendar event date/time format
     */
    const CALENDAR_DATETIME_FORMAT = 'Y-m-d H:i';
    const CALENDAR_TIME_FORMAT = 'H:i';
    /**#@-*/

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @var AvailableTicketResolver
     */
    private $resolver;

    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @var LocaleResolver
     */
    private $locale;

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @param ArrayManager $arrayManager
     * @param AvailableTicketResolver $resolver
     * @param StockManagementInterface $stockManagement
     * @param LocaleResolver $locale
     * @param SectorRepositoryInterface $sectorRepository
     * @param array $providers
     */
    public function __construct(
        ArrayManager $arrayManager,
        AvailableTicketResolver $resolver,
        StockManagementInterface $stockManagement,
        LocaleResolver $locale,
        SectorRepositoryInterface $sectorRepository,
        array $providers = []
    ) {
        $this->arrayManager = $arrayManager;
        $this->providers = $providers;
        $this->resolver = $resolver;
        $this->stockManagement = $stockManagement;
        $this->locale = $locale;
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function process($jsLayout, $product)
    {
        if ($product->getAwEtScheduleType() != ScheduleType::RECURRING) {
            return $jsLayout;
        }

        $optionsProviderPath = 'components/awEtViewOptionsProvider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            [
                'data' => [
                    'recurring' => $this->getConfig($product)
                ]
            ]
        );

        return $jsLayout;
    }

    /**
     * Get provider configs
     *
     * @param Product $product
     * @return array[]
     * @throws \Exception
     */
    private function getConfig($product)
    {
        $recurringSchedule = $product->getExtensionAttributes()->getAwEtRecurringSchedule();
        $sectorConfig = $product->getExtensionAttributes()->getAwEtSectorConfig();
        if (!$recurringSchedule || !$sectorConfig) {
            return [];
        }
        $config = $this->getMainConfig($recurringSchedule, $sectorConfig, $product->getStore());
        if (isset($this->providers[$recurringSchedule->getType()])) {
            $provider = $this->providers[$recurringSchedule->getType()];
            if ($provider instanceof ProviderInterface) {
                $config = array_merge($config, $provider->getConfig($recurringSchedule));
            }
        }

        return $config;
    }

    /**
     * Get config for all recurring schedule types
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @param Sector[] $sectorConfig
     * @param Store $store
     * @return array
     * @throws \Exception
     */
    private function getMainConfig($recurringSchedule, $sectorConfig, $store)
    {
        $unavailableEvents = [];
        $unavailableSector = [];
        $sectorQty = [];
        try {
            $sectorDefaultQty = $this->resolver->resolveDefaultQtyForSectors($sectorConfig);
            $purchasedTickets =
                $this->resolver->getPurchasedTicketsForRecurringEvent(
                    $recurringSchedule, array_keys($sectorDefaultQty)
                );
            foreach ($purchasedTickets as $purchasedTicket) {
                $sectorId = $purchasedTicket[TicketInterface::SECTOR_ID];
                $defaultTicketQty = $sectorDefaultQty[$sectorId];
                $ticketQty = $purchasedTicket[AdditionalProductOptionsInterface::QTY];
                $eventId = $this->prepareCalendarEventId($purchasedTicket[TicketInterface::EVENT_START_DATE]);
                $qty = (int)($defaultTicketQty - $ticketQty);

                if ($ticketQty >= $defaultTicketQty) {
                    $unavailableSector[$eventId][$sectorId] = $eventId;
                    $qty = 0;
                }
                $sectorQty[$eventId][$sectorId] = $qty;
            }
            foreach ($unavailableSector as $eventId => $sectors) {
                if (count($sectors) == count($sectorDefaultQty)) {
                    $unavailableEvents[] = $eventId;
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return [
            'unavailableEvents' => \Zend_Json::encode($unavailableEvents),
            'timeSlots' => $this->prepareTimeSlots($recurringSchedule->getTimeSlots()),
            'daysToDisplay' => $recurringSchedule->getDaysToDisplay(),
            'locale' => $this->getLocale(),
            'sectorDefaultQty' => $sectorDefaultQty,
            'sectorQty' => $sectorQty,
            'timezone' => $store->getConfig(Config::XML_PATH_GENERAL_LOCALE_TIMEZONE),
            'deadlineCorrections' => $this->getDeadlineCorrections($recurringSchedule),
            'displayedTime' => $this->getMinMaxTime($recurringSchedule->getTimeSlots()),
            'isTimeSlotMultiSelectionAllowed' => (bool)$recurringSchedule->getMultiselectionTimeSlots()
        ];
    }

    /**
     * Prepare id for calendar event
     *
     * @param string $date
     * @return string
     * @throws \Exception
     */
    private function prepareCalendarEventId($date)
    {
        $date = new \DateTime($date);

        return $date->format(self::CALENDAR_DATETIME_FORMAT);
    }

    /**
     * Prepare array of TimeSlots with start/end time
     *
     * @param TimeSlotInterface[] $timeSlots
     * @return array
     * @throws \Exception
     */
    private function prepareTimeSlots($timeSlots)
    {
        $result = [];
        foreach ($timeSlots as $timeSlot) {
            $startTime = new \DateTime($timeSlot->getStartTime());
            $endTime = new \DateTime($timeSlot->getEndTime());
            $startTime = $startTime->format(self::CALENDAR_TIME_FORMAT);
            $endTime = $endTime->format(self::CALENDAR_TIME_FORMAT);
            $result[$timeSlot->getId()] =
                [
                    'startTime' => $startTime,
                    'endTime' => $endTime
                ];
        }

        return $result;
    }

    /**
     * Get deadline corrections if set
     *
     * @param ProductRecurringScheduleInterface $recurringSchedule
     * @return array|null
     */
    private function getDeadlineCorrections($recurringSchedule)
    {
        if ($recurringSchedule->getSellingDeadlineType() == TicketSellingDeadline::IN_ADVANCE) {
            $deadlineCorrections = $recurringSchedule->getSellingDeadlineCorrection();
            return [
                'days' => $deadlineCorrections->getDays(),
                'hours' => $deadlineCorrections->getHours(),
                'minutes' => $deadlineCorrections->getMinutes()
            ];
        }

        return null;
    }

    /**
     * Get current store locale
     *
     * @return string
     */
    private function getLocale()
    {
        return str_replace('_', '-', $this->locale->getLocale());
    }

    /**
     * Get min & max displayed calendar time
     *
     * @param TimeSlotInterface[] $timeSlots
     * @return array
     */
    private function getMinMaxTime($timeSlots)
    {
        $minTime = new \DateTime('23:59');
        $maxTime = new \DateTime('00:00');

        foreach ($timeSlots as $timeSlot) {
            $timeSlotStartTime = new \DateTime($timeSlot->getStartTime());
            $timeSlotEndTime = new \DateTime($timeSlot->getEndTime());

            if (strtotime($minTime->format(self::CALENDAR_TIME_FORMAT)) >
                strtotime($timeSlotStartTime->format(self::CALENDAR_TIME_FORMAT))
            ) {
                $minTime = $timeSlotStartTime;
            }
            if (strtotime($maxTime->format(self::CALENDAR_TIME_FORMAT)) <
                strtotime($timeSlotEndTime->format(self::CALENDAR_TIME_FORMAT))
            ) {
                $maxTime = $timeSlotEndTime;
            }
        }

        return [
            'minTime' => $minTime->format(self::CALENDAR_TIME_FORMAT),
            'maxTime' => $maxTime->format(self::CALENDAR_TIME_FORMAT)
        ];
    }
}
