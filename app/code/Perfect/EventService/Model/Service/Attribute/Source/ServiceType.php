<?php

namespace Perfect\EventService\Model\Service\Attribute\Source;

/**
 * Class ServiceType
 *
 * @package Perfect\EventService\Model\Service\Attribute\Source
 */
class ServiceType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Option values
     */
    const VALUE_INDIVIDUAL = 0;

    const VALUE_GROUP = 1;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Individual'), 'value' => self::VALUE_INDIVIDUAL],
                ['label' => __('Group'), 'value' => self::VALUE_GROUP],
            ];
        }
        return $this->_options;
    }
}