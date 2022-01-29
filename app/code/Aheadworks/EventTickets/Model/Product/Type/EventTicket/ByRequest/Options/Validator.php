<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Validator
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Options
 */
class Validator extends AbstractValidator
{
    /**
     * @var ProductPersonalOptionInterface
     */
    private $option;

    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

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
     * Get option
     *
     * @return ProductPersonalOptionInterface
     */
    public function getOption()
    {
        if ($this->option instanceof ProductPersonalOptionInterface) {
            return $this->option;
        }
        throw new \InvalidArgumentException('Option should implement ProductPersonalOptionInterface');
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
        if (isset($this->validators[$this->getOption()->getType()])) {
            $validator = $this->validators[$this->getOption()->getType()];
            $validator
                ->setOption($this->getOption())
                ->isValid($attendeeValue);
            $this->_addMessages($validator->getMessages());
        }

        return empty($this->getMessages());
    }
}
