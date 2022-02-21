<?php

namespace Perfect\Service\Ui\Component\Service\Form;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerPosition
 *
 * @package Perfect\Service\Ui\Component\Service\Form
 */
class CustomerPosition implements OptionSourceInterface
{
    const CUSTOMER_POSITION_ATTRIBUTE_CODE = 'job_position';

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    protected $options;

    /**
     * CustomerPosition constructor.
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->getEmployeePositions();
        }

        return $this->options;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEmployeePositions()
    {
        $attribute = $this->eavConfig->getAttribute('customer', self::CUSTOMER_POSITION_ATTRIBUTE_CODE);

        return $attribute->getSource()->getAllOptions();
    }
}