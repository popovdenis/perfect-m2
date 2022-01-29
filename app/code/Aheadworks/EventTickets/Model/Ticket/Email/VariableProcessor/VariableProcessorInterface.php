<?php
namespace Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor;

/**
 * Interface VariableProcessorInterface
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Email\VariableProcessor
 */
interface VariableProcessorInterface
{
    /**
     * Prepare variables before send
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables);
}
