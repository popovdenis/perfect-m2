<?php

namespace Perfect\Service\Ui\Component\Service\Form;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerLevel
 *
 * @package Perfect\Service\Ui\Component\Service\Form
 */
class CustomerLevel implements OptionSourceInterface
{
    const CUSTOMER_LEVEL_ATTRIBUTE_CODE = 'skill_level';

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
            $this->options = $this->getEmployeeLevels();
        }

        return $this->options;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEmployeeLevels()
    {
        $attribute = $this->eavConfig->getAttribute('customer', self::CUSTOMER_LEVEL_ATTRIBUTE_CODE);

        return $attribute->getSource()->getAllOptions();
    }
}