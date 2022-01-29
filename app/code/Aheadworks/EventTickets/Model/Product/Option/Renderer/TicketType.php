<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Framework\Escaper;

/**
 * Class TicketType
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Renderer
 */
class TicketType implements RendererInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @param Escaper $escaper
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     */
    public function __construct(
        Escaper $escaper,
        TicketTypeRepositoryInterface $ticketTypeRepository
    ) {
        $this->escaper = $escaper;
        $this->ticketTypeRepository = $ticketTypeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function render($options)
    {
        $result = [];
        if (!$options->getAwEtTicketTypeId()) {
            return $result;
        }
        $ticketTypeLabel = $this->getTicketTypeLabel($options->getAwEtTicketTypeId());
        $result[] = [
            'label' => __('Ticket Type'),
            'value' => $this->escaper->escapeHtml($ticketTypeLabel)
        ];

        return $result;
    }

    /**
     * Retrieve ticket type label
     *
     * @param int $ticketTypeId
     * @return string
     */
    private function getTicketTypeLabel($ticketTypeId)
    {
        $ticketTypeLabel = '';
        try {
            /** @var TicketTypeInterface $ticketType */
            $ticketType = $this->ticketTypeRepository->get($ticketTypeId);
            $ticketTypeLabel = $ticketType->getCurrentLabels()->getTitle();
        } catch (\Exception $exception) {
        }
        return $ticketTypeLabel;
    }
}
