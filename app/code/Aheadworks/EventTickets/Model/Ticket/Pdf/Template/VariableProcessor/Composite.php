<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor;

use Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor\VariableProcessorInterface;

/**
 * Class Composite
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor
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
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        foreach ($this->processors as $processor) {
            $variables = $processor->prepareVariables($variables);
        }
        return $variables;
    }
}
