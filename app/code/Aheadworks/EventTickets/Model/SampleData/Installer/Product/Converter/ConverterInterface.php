<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter;

/**
 * Interface ConverterInterface
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter
 */
interface ConverterInterface
{
    /**
     * Convert CSV format row to array
     *
     * @param array $row
     * @return array
     */
    public function convertRow($row);
}
