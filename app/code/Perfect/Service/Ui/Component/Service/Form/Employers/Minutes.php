<?php

namespace Perfect\Service\Ui\Component\Service\Form\Employers;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Minutes
 *
 * @package Perfect\Service\Ui\Component\Service\Form\Employers
 */
class Minutes implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getMinutes();
    }

    public function getMinutes()
    {
        $output = [];

        for ($i = 0; $i < 60;) {
            $output[] = [
                'value' => $i,
                'label' => sprintf('%s м', $i)
            ];
            $i += 5;
        }

        return $output;
    }
}