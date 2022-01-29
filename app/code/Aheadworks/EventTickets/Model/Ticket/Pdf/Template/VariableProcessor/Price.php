<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\PdfVariables;
use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\VariableProcessorInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\EventTickets\Model\Ticket\Price\Resolver
    as TicketPriceResolver;

/**
 * Class Price
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor
 */
class Price implements VariableProcessorInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var TicketPriceResolver
     */
    private $ticketPriceResolver;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param TicketPriceResolver $ticketPriceResolver
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        TicketPriceResolver $ticketPriceResolver
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->ticketPriceResolver = $ticketPriceResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var TicketInterface $ticket */
        $ticket = $variables[PdfVariables::TICKET];
        /** @var StoreInterface $store */
        $store = $variables[PdfVariables::STORE];
        $variables[PdfVariables::PRICE_FORMATTED] =
            $this
                ->priceCurrency
                ->convertAndFormat(
                    $this->ticketPriceResolver->getPriceToShow($ticket),
                    false,
                    PriceCurrencyInterface::DEFAULT_PRECISION,
                    $store
                )
        ;

        return $variables;
    }
}
