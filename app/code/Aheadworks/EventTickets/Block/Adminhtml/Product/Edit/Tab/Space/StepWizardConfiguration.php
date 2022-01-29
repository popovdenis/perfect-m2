<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Product\Edit\Tab\Space;

use Magento\Backend\Block\Template;

/**
 * Class StepWizardConfiguration
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Product\Edit\Tab\Space
 */
class StepWizardConfiguration extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_EventTickets::catalog/product/new/space/wizard_config.phtml';

    /**
     * Render space wizard steps
     *
     * @param array $initData
     * @return string
     */
    public function renderSpaceWizardSteps($initData = [])
    {
        /** @var \Magento\Ui\Block\Component\StepsWizard $wizardBlock */
        $wizardBlock = $this->getChildBlock($this->getData('config/stepWizardName'));
        if ($wizardBlock) {
            $wizardBlock->setInitData($initData);
            return $wizardBlock->toHtml();
        }
        return '';
    }
}
