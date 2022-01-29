<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\PostDataProcessor;

use Aheadworks\EventTickets\Model\PostDataProcessorInterface;

/**
 * Class Composite
 * @package Aheadworks\EventTickets\Model\Product\Additional\PostDataProcessor
 */
class Composite implements PostDataProcessorInterface
{
    /**
     * @var PostDataProcessorInterface[]
     */
    private $processors;

    /**
     * @param PostDataProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareEntityData($data)
    {
        foreach ($this->processors as $processor) {
            $data = $processor->prepareEntityData($data);
        }
        return $data;
    }
}
