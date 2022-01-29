<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options;

/**
 * Class DropdownValidator
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options
 */
class DropdownValidator extends DefaultValidator
{
    /**
     * {@inheritdoc}
     */
    public function isValid($attendeeValue)
    {
        $required = $this->isRequired($attendeeValue);
        $optionValues = $this->option->getValues();

        $optionValueFound = false;
        foreach ($optionValues as $optionValue) {
            if ($optionValue->getId() == $attendeeValue) {
                $optionValueFound = true;
                break;
            }
        }
        if (!$optionValueFound && $required) {
            $this->_addMessages(
                [sprintf('Incorrect value in field %s.', $this->option->getCurrentLabels()->getTitle())]
            );
        }

        return empty($this->getMessages());
    }
}
