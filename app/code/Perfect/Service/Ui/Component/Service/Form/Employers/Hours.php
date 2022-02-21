<?php

namespace Perfect\Service\Ui\Component\Service\Form\Employers;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Hours
 *
 * @package Perfect\Service\Ui\Component\Service\Form\Employers
 */
class Hours implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getTimes();
    }

    protected function getTimes()
    {
        $output = [];

        for ($i = 1; $i < 24; $i++) {
            $output[] = [
                'value' => $i,
                'label' => sprintf('%s ч', $i)
            ];
        }

        return $output;
    }
}