<?php
namespace Aheadworks\EventTickets\Model\BuyRequest;

use Magento\Framework\DataObject;
use Aheadworks\EventTickets\Api\Data\BuyRequest\AttendeeOptionInterface;

/**
 * Class AttendeeOption
 * @package Aheadworks\EventTickets\Model\BuyRequest
 */
class AttendeeOption extends DataObject implements AttendeeOptionInterface
{
    /**
     * @inheritdoc
     */
    public function getTicketNumber()
    {
        return $this->getData(self::TICKET_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setTicketNumber($number)
    {
        return $this->setData(self::TICKET_NUMBER, $number);
    }

    /**
     * @inheritdoc
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOptionId($id)
    {
        return $this->setData(self::OPTION_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setOptionValue($value)
    {
        return $this->setData(self::OPTION_VALUE, $value);
    }
}
