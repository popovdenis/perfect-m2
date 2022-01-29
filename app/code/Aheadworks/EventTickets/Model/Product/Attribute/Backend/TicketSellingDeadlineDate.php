<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TicketSellingDeadlineDate
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class TicketSellingDeadlineDate extends Datetime
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        if ($object->getData(ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE) == ScheduleType::ONE_TIME) {
            $ticketSellingDeadline = $object->getData(ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE);
            if ($ticketSellingDeadline != TicketSellingDeadline::CUSTOM_DATE) {
                return true;
            }

            parent::validate($object);
            $ticketSellingDeadlineDate = new \DateTime(
                $this->formatDate($object->getData($this->getAttribute()->getName()))
            );
            $endDate = new \DateTime(
                $this->formatDate($object->getData(ProductAttributeInterface::CODE_AW_ET_END_DATE))
            );

            if ($ticketSellingDeadlineDate > $endDate) {
                throw new LocalizedException(
                    __('Tickets Selling Deadline Custom Date cannot be later than Event End Date.')
                );
            }
        }

        return true;
    }
}
