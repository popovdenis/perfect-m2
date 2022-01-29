<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class DefaultValidator
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options
 */
class DefaultValidator extends AbstractValidator
{
    /**
     * @var ProductPersonalOptionInterface
     */
    protected $option;

    /**
     * Set option
     *
     * @param ProductPersonalOptionInterface $option
     * @return $this
     */
    public function setOption($option)
    {
        $this->option = $option;
        return $this;
    }

    /**
     * Returns true if and only if entity meets the validation requirements
     *
     * @param mixed $attendeeValue
     * @return bool
     */
    public function isValid($attendeeValue)
    {
        $this->_clearMessages();

        $this->isRequired($attendeeValue);

        return empty($this->getMessages());
    }

    /**
     * Check if require
     *
     * @param mixed $attendeeValue
     * @return bool
     */
    protected function isRequired($attendeeValue)
    {
        if ($this->option->isRequire() && empty($attendeeValue)) {
            $this->_addMessages([sprintf('%s is required.', $this->option->getCurrentLabels()->getTitle())]);
        }
        return !empty($this->getMessages());
    }
}
