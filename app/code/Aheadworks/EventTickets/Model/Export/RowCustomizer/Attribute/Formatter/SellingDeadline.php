<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline
    as TicketSellingDeadlineAttributeSource;

/**
 * Class SellingDeadline
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class SellingDeadline implements FormatterInterface
{
    /**
     * @var TicketSellingDeadlineAttributeSource
     */
    private $ticketSellingDeadlineAttributeSource;

    /**
     * @param TicketSellingDeadlineAttributeSource $ticketSellingDeadlineAttributeSource
     */
    public function __construct(
        TicketSellingDeadlineAttributeSource $ticketSellingDeadlineAttributeSource
    ) {
        $this->ticketSellingDeadlineAttributeSource = $ticketSellingDeadlineAttributeSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        $formattedValue = $this->ticketSellingDeadlineAttributeSource->getOptionText($value);
        return $formattedValue;
    }
}
