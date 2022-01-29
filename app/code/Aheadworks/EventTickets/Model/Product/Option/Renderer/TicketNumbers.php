<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

use Magento\Framework\Escaper;

/**
 * Class TicketNumbers
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Renderer
 */
class TicketNumbers implements RendererInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Escaper $escaper
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function render($options)
    {
        $result = [];
        $numbers = $options->getAwEtTicketNumbers();
        if (!is_array($numbers) || (is_array($numbers) && !count($numbers))) {
            return $result;
        }

        $result[] = [
            'label' => __('Ticket Numbers'),
            'value' => implode(', ', $this->escaper->escapeHtml($numbers)),
            'custom_view' => true
        ];

        return $result;
    }
}
