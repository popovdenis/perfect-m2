<?php
namespace Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor;

/**
 * Class Composite
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor
 */
class Composite
{
    /**
     * @var VariableProcessorInterface[]
     */
    private $processors;

    /**
     * @param VariableProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare variables
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables)
    {
        foreach ($this->processors as $processor) {
            $variables = $processor->prepareVariables($variables);
        }
        return $variables;
    }
}
