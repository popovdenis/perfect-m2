<?php
namespace Aheadworks\EventTickets\Model\Import;

/**
 * Class ArrayProcessor
 * @package Aheadworks\EventTickets\Model\Import
 */
class ArrayProcessor
{
    /**
     * Remove empty values and sub arrays
     *
     * @param array $array
     * @return array
     */
    public function removeEmptyValuesAndSubArrays($array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->removeEmptyValuesAndSubarrays($value);
                if (!sizeof($value)) {
                    unset($array[$key]);
                }
            } elseif (!strlen($value)) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
