<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean as BooleanAttributeSource;

/**
 * Class RequireShipping
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class RequireShipping implements FormatterInterface
{
    /**
     * @var BooleanAttributeSource
     */
    private $booleanAttributeSource;

    /**
     * @param BooleanAttributeSource $booleanAttributeSource
     */
    public function __construct(
        BooleanAttributeSource $booleanAttributeSource
    ) {
        $this->booleanAttributeSource = $booleanAttributeSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        $formattedValue = $this->booleanAttributeSource->getOptionText($value);
        return $formattedValue;
    }
}
