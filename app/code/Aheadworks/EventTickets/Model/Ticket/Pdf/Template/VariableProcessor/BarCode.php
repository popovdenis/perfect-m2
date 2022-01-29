<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\PdfVariables;
use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\VariableProcessorInterface;

/**
 * Class BarCode
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor
 */
class BarCode implements VariableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var TicketInterface $ticket */
        $ticket = $variables[PdfVariables::TICKET];
        $variables[PdfVariables::BAR_CODE] =
            '<barcode code="' . $ticket->getNumber() . '" size="1.3" type="C128A" class="barcode" />';

        return $variables;
    }
}
