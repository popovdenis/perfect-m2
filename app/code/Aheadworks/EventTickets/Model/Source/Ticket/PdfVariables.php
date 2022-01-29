<?php
namespace Aheadworks\EventTickets\Model\Source\Ticket;

/**
 * Class PdfVariables
 *
 * @package Aheadworks\EventTickets\Model\Source\Ticket
 */
class PdfVariables extends EmailVariables
{
    /**#@+
     * Ticket pdf variables
     */
    const QR_CODE = 'qrCode';
    const BAR_CODE = 'barCode';
    const ATTENDEE = 'attendee';
    const PRICE_FORMATTED = 'priceFormatted';
    /**#@-*/
}
