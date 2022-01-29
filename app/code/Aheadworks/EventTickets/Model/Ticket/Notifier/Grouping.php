<?php
namespace Aheadworks\EventTickets\Model\Ticket\Notifier;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class Grouping
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Notifier
 */
class Grouping
{
    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @var string[]
     */
    private $groupByFields;

    /**
     * @var array
     */
    private $ticketObjectMethods;

    /**
     * @var array
     */
    private $ticketExtensionObjectMethods;

    /**
     * @var int
     */
    private $countFields = 0;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @var TicketInterface[]
     */
    private $ticketsGrouped = [];

    /**
     * @var array
     */
    private $groupedFilters = [];

    /**
     * @param SimpleDataObjectConverter $simpleDataObjectConverter
     * @param string[] $groupByFields
     */
    public function __construct(
        SimpleDataObjectConverter $simpleDataObjectConverter,
        $groupByFields = []
    ) {
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->groupByFields = $groupByFields;
        $this->countFields = count($this->groupByFields);
    }

    /**
     * Process grouping
     *
     * @param TicketInterface[] $tickets
     * @return array
     */
    public function process($tickets)
    {
        $this->reset();
        foreach ($tickets as $ticket) {
            $newGroup = [];
            foreach ($this->groupByFields as $field) {
                if ($methodName = $this->getMethodByFieldName($ticket, $field)) {
                    $newGroup[$field] = $ticket->{$methodName}();
                } elseif ($methodName = $this->getMethodByFieldName($ticket, $field, true)) {
                    $newGroup[$field] = $ticket->getExtensionAttributes()->{$methodName}();
                }
            }
            $matchedFields = 0;
            $groupAlias = '';
            foreach ($this->groupedFilters as $groupKey => $group) {
                $matchedFields = 0;
                $groupAlias = $groupKey;
                foreach ($group as $key => $value) {
                    if (isset($newGroup[$key]) && $value == $newGroup[$key]) {
                        $matchedFields++;
                    }
                }
                if ($this->countFields == $matchedFields) {
                    break;
                }
            }
            if ($this->countFields != $matchedFields) {
                $groupAlias = 'group-' . $this->getNextIncrement();
                $this->groupedFilters[$groupAlias] = $newGroup;
            }
            $this->ticketsGrouped[$groupAlias][] = $ticket;
        }
        return $this->ticketsGrouped;
    }

    /**
     * Reset fields
     *
     * @return void
     */
    private function reset()
    {
        $this->counter = 0;
        $this->ticketsGrouped = [];
        $this->groupedFilters = [];
    }

    /**
     * Retrieve method by field name
     *
     * @param TicketInterface $ticket
     * @param string $field
     * @param bool $searchInExtensionAttributes
     * @return string
     */
    private function getMethodByFieldName($ticket, $field, $searchInExtensionAttributes = false)
    {
        if (null === $this->ticketObjectMethods) {
            $this->ticketObjectMethods = get_class_methods(get_class($ticket));
        }
        if (null === $this->ticketExtensionObjectMethods) {
            $this->ticketExtensionObjectMethods = [];
            if ($ticket->getExtensionAttributes()) {
                $this->ticketExtensionObjectMethods = get_class_methods(
                    get_class($ticket->getExtensionAttributes())
                );
            }
        }
        $camelCaseField = $this->simpleDataObjectConverter->snakeCaseToUpperCamelCase($field);
        $possibleMethods = [
            'get' . $camelCaseField,
            'is' . $camelCaseField,
        ];
        $ticketMethods = $searchInExtensionAttributes
            ? $this->ticketExtensionObjectMethods
            : $this->ticketObjectMethods;

        $methodNames = array_intersect($possibleMethods, $ticketMethods);
        $methodName = array_shift($methodNames);

        return $methodName;
    }

    /**
     * Increment and return counter
     *
     * @return int
     */
    private function getNextIncrement()
    {
        return ++$this->counter;
    }
}
