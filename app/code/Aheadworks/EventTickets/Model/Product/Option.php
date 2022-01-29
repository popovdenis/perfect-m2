<?php
namespace Aheadworks\EventTickets\Model\Product;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Option
 *
 * @package Aheadworks\EventTickets\Model\Product
 */
class Option extends AbstractExtensibleModel implements OptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAwEtAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtSectorId()
    {
        return $this->getData(self::SECTOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtSectorId($sectorId)
    {
        return $this->setData(self::SECTOR_ID, $sectorId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtTicketTypeId()
    {
        return $this->getData(self::TICKET_TYPE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtTicketTypeId($ticketTypeId)
    {
        return $this->setData(self::TICKET_TYPE_ID, $ticketTypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtTicketNumbers()
    {
        return $this->getData(self::TICKET_NUMBERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtTicketNumbers($ticketNumbers)
    {
        return $this->setData(self::TICKET_NUMBERS, $ticketNumbers);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtOptionAttendeeIds()
    {
        return $this->getData(self::OPTION_ATTENDEE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtOptionAttendeeIds($optionAttendeeIds)
    {
        return $this->setData(self::OPTION_ATTENDEE_IDS, $optionAttendeeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtAttendeeIds()
    {
        return $this->getData(self::ATTENDEE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtAttendeeIds($attendeeIds)
    {
        return $this->setData(self::ATTENDEE_IDS, $attendeeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtAttendees()
    {
        return $this->getData(self::ATTENDEES);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtAttendees($attendees)
    {
        return $this->setData(self::ATTENDEES, $attendees);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtRecurringStartDate()
    {
        return $this->getData(self::RECURRING_START_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtRecurringStartDate($startDate)
    {
        return $this->setData(self::RECURRING_START_DATE, $startDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtRecurringEndDate()
    {
        return $this->getData(self::RECURRING_END_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtRecurringEndDate($endDate)
    {
        return $this->setData(self::RECURRING_END_DATE, $endDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtRecurringTimeSlotId()
    {
        return $this->getData(self::RECURRING_TIME_SLOT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtRecurringTimeSlotId($timeSlotId)
    {
        return $this->setData(self::RECURRING_TIME_SLOT_ID, $timeSlotId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectorId()
    {
        return $this->getData(self::BUY_REQUEST_SECTOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectorId($id)
    {
        return $this->setData(self::BUY_REQUEST_SECTOR_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId()
    {
        return $this->getData(self::BUY_REQUEST_TYPE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeId($id)
    {
        return $this->setData(self::BUY_REQUEST_TYPE_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(self::BUY_REQUEST_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::BUY_REQUEST_QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtSlots()
    {
        return $this->getData(self::BUY_REQUEST_AW_ET_SLOTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtSlots($slots)
    {
        return $this->setData(self::BUY_REQUEST_AW_ET_SLOTS, $slots);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwEtTickets()
    {
        return $this->getData(self::BUY_REQUEST_AW_ET_TICKETS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwEtTickets($tickets)
    {
        return $this->setData(self::BUY_REQUEST_AW_ET_TICKETS, $tickets);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttendeeOptions()
    {
        return $this->getData(self::BUY_REQUEST_ATTENDEE_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttendeeOptions($options)
    {
        return $this->setData(self::BUY_REQUEST_ATTENDEE_OPTIONS, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\OptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
