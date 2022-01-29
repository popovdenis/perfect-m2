<?php
namespace Aheadworks\EventTickets\Model\Import\Processor;

/**
 * Class Composite
 * @package Aheadworks\EventTickets\Model\Import\Processor
 */
class Composite implements ProcessorInterface
{
    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function processData($rowData, $entity)
    {
        foreach ($this->processors as $processor) {
            $rowData = $processor->processData($rowData, $entity);
        }
        return $rowData;
    }
}
