<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Product\Space\Steps;

use Magento\Ui\Block\Component\StepsWizard\StepAbstract;

/**
 * Class SelectSpace
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Product\Space\Steps
 */
class SelectSpace extends StepAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_EventTickets::catalog/product/new/space/steps/select_space.phtml';

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Select Space');
    }
}
