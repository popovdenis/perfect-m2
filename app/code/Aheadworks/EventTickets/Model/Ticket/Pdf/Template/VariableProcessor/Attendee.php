<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\PdfVariables;
use Aheadworks\EventTickets\Model\Ticket;
use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\VariableProcessorInterface;

/**
 * Class Attendee
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor
 */
class Attendee implements VariableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var TicketInterface|Ticket $ticket */
        $ticket = $variables[PdfVariables::TICKET];
        $variables[PdfVariables::ATTENDEE] = $ticket->getOptionByType(ProductPersonalOptionInterface::OPTION_TYPE_NAME);

        return $variables;
    }
}
