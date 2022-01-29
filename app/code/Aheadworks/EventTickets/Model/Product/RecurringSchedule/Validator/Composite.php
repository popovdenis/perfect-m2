<?php
namespace Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator;

use Aheadworks\EventTickets\Api\Data\ProductRecurringScheduleInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Composite
 * @package Aheadworks\EventTickets\Model\Product\RecurringSchedule\Validator
 */
class Composite extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * @inheritDoc
     * @param ProductRecurringScheduleInterface $recurringSchedule
     */
    public function isValid($recurringSchedule)
    {
        if ($recurringSchedule->hasIsValid()) {
            $isValid = $recurringSchedule->getIsValid();
        } else {
            foreach ($this->validators as $validator) {
                if (!$validator->isValid($recurringSchedule)) {
                    $this->_addMessages($validator->getMessages());
                    break;
                }
            }
            $isValid = empty($this->getMessages());
            $recurringSchedule->setIsValid((bool)$isValid);
        }

        return $isValid;
    }
}
