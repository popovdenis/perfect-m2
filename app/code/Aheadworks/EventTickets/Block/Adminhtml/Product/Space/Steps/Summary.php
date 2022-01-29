<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Product\Space\Steps;

use Magento\Ui\Block\Component\StepsWizard\StepAbstract;

/**
 * Class Summary
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Product\Space\Steps
 */
class Summary extends StepAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_EventTickets::catalog/product/new/space/steps/summary.phtml';

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Summary');
    }
}
