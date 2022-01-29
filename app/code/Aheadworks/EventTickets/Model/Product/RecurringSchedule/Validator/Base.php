<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\TicketSellingDeadline;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Base
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator
 */
class Base extends AbstractValidator
{
    /**
     * @inheritDoc
     * @param ProductRecurringScheduleInterface $recurringSchedule
     */
    public function isValid($recurringSchedule)
    {
        $errors = [];

        if (!\Zend_Validate::is($recurringSchedule->getSellingDeadlineType(), 'NotEmpty')) {
            $errors[] = __('Tickets Selling Deadline can\'t be empty.');
        }
        if (!in_array($recurringSchedule->getSellingDeadlineType(), TicketSellingDeadline::getOptionValues())) {
            $errors[] = __('Unsupported Tickets Selling Deadline value.');
        }

        $this->_addMessages($errors);

        return empty($this->getMessages());
    }
}
