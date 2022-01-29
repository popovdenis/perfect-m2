<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column\Recurring;

use Aheadworks\EventTickets\Ui\Component\Listing\Column\EventProductActions as OneTimeEventProductActions;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class EventProductActions
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column\Recurring
 */
class EventProductActions extends OneTimeEventProductActions
{
    /**
     * Url path
     */
    const VIEW_TICKETS_URL_PATH = 'aw_event_tickets/recurring_ticket/index';

    /**
     * Retrieve url
     *
     * @param array $itemData
     * @return string
     */
    protected function getUrl($itemData)
    {
        $eventDate = new \DateTime($itemData['event_date']);
        return $this->urlBuilder->getUrl(
            self::VIEW_TICKETS_URL_PATH,
            [
                'product_id' => $itemData['entity_id'],
                'event_date' => $eventDate->format(DateTime::DATE_PHP_FORMAT),
                'slot_id' => $itemData['recurring_time_slot_id']
            ]
        );
    }
}
