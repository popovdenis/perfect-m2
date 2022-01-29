<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Product\Space\Steps;

use Magento\Ui\Block\Component\StepsWizard\StepAbstract;

/**
 * Class SelectVenue
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Product\Space\Steps
 */
class SelectVenue extends StepAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_EventTickets::catalog/product/new/space/steps/select_venue.phtml';

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Select Venue');
    }
}
