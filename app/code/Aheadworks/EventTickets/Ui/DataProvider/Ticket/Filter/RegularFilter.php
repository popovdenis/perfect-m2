<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Ticket\Filter;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\Element\UiComponent\DataProvider\RegularFilter as FrameworkRegularFilter;

/**
 * Class RegularFilter
 * @package Aheadworks\EventTickets\Ui\DataProvider\Ticket\Filter
 */
class RegularFilter extends FrameworkRegularFilter
{
    /**
     * @inheritDoc
     */
    public function apply(Collection $collection, Filter $filter)
    {
        if ($filter->getField() == 'event_date') {
            $eventDate = new \DateTime($filter->getValue());
            $collection
                ->addFieldToFilter(
                    TicketInterface::EVENT_START_DATE,
                    ['gteq' => $eventDate->format(DateTime::DATE_PHP_FORMAT)]
                )
                ->addFieldToFilter(
                    TicketInterface::EVENT_END_DATE,
                    ['lt' => $eventDate->modify('+1day')->format(DateTime::DATE_PHP_FORMAT)]
                );
        } else {
            parent::apply($collection, $filter);
        }
    }
}
