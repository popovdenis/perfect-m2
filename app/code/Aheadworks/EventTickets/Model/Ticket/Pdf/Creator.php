<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Pdf\Template\Builder;
use Aheadworks\EventTickets\Model\Ticket\Pdf\Template\Processor;
use Aheadworks\EventTickets\Model\Ticket\Pdf\DocumentFactory;

/**
 * Class Creator
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf
 */
class Creator
{
    /**
     * @var Builder
     */
    private $templateBuilder;

    /**
     * @var Processor
     */
    private $templateProcessor;

    /**
     * @var DocumentFactory
     */
    private $pdfDocumentFactory;

    /**
     * @param Builder $templateBuilder
     * @param Processor $templateProcessor
     * @param DocumentFactory $pdfDocumentFactory
     */
    public function __construct(
        Builder $templateBuilder,
        Processor $templateProcessor,
        DocumentFactory $pdfDocumentFactory
    ) {
        $this->templateBuilder = $templateBuilder;
        $this->templateProcessor = $templateProcessor;
        $this->pdfDocumentFactory = $pdfDocumentFactory;
    }

    /**
     * Create ticket in pdf file
     *
     * @param TicketInterface $ticket
     * @param bool $forDownload
     * @return array|string
     */
    public function create($ticket, $forDownload)
    {
        $bodyHtml = $this->buildPdfTemplate($ticket);
        /** @var Document $document */
        $document = $this->pdfDocumentFactory->create();

        if ($forDownload) {
            return ['type' => 'string', 'value' => $document->createFromHtml($bodyHtml), 'rm' => true];
        } else {
            return $document->createFromHtml($bodyHtml);
        }
    }

    /**
     * Build Pdf template as html
     *
     * @param TicketInterface $ticket
     * @return string
     */
    private function buildPdfTemplate($ticket)
    {
        list($templateId, $options, $variables) = $this->templateProcessor->process($ticket);

        $body = $this->templateBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions($options)
            ->setTemplateVars($variables)
            ->build();

        return $body;
    }
}
