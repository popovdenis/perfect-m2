<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class TicketType
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class TicketType implements FormatterInterface
{
    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     */
    public function __construct(
        TicketTypeRepositoryInterface $ticketTypeRepository
    ) {
        $this->ticketTypeRepository = $ticketTypeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        try {
            $formattedValue = $this->ticketTypeRepository->get($value)->getName();
        } catch (NoSuchEntityException $exception) {
            $formattedValue = '';
        }
        return $formattedValue;
    }
}
