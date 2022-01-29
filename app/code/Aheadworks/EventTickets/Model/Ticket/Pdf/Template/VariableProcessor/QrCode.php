<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\PdfVariables;
use Aheadworks\EventTickets\Model\Ticket\Email\UrlBuilder;
use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\VariableProcessorInterface;
use Aheadworks\EventTickets\Model\Url\ParamEncryptor;

/**
 * Class QrCode
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor
 */
class QrCode implements VariableProcessorInterface
{
    /**
     * @var ParamEncryptor
     */
    private $encryptor;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param ParamEncryptor $encryptor
     * @param UrlBuilder $urlBuilder
     */
    public function __construct(ParamEncryptor $encryptor, UrlBuilder $urlBuilder)
    {
        $this->encryptor = $encryptor;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var TicketInterface $ticket */
        $ticket = $variables[PdfVariables::TICKET];
        $variables[PdfVariables::QR_CODE] =
            '<barcode code="' . $this->getTicketUrl($ticket) . '" size="2.5" type="QR" error="M" class="qr-code" />';

        return $variables;
    }

    /**
     * Retrieve ticket url
     *
     * @param TicketInterface $ticket
     * @return string
     */
    private function getTicketUrl($ticket)
    {
        $params = [
            'key' => $this->encryptor->encrypt(['ticket_number' => $ticket->getNumber(), 'checkIn' => true]),
            '_nosid' => true
        ];

        $url = $this->urlBuilder->getUrl(
            'aw_event_tickets/ticket/management',
            $ticket->getStoreId(),
            $params
        );

        return $url;
    }
}
