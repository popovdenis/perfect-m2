<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter;

/**
 * Class Composite
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter
 */
class Composite implements ConverterInterface
{
    /**
     * @var ConverterInterface[]
     */
    private $converters;

    /**
     * @param ConverterInterface[] $converters
     */
    public function __construct(array $converters = [])
    {
        $this->converters = $converters;
    }

    /**
     * {@inheritdoc}
     */
    public function convertRow($row)
    {
        $data = [];
        foreach ($this->converters as $converter) {
            $data = array_merge($data, $converter->convertRow($row));
        }
        return $data;
    }
}
