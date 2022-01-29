<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Venue\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Save
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Venue\Edit\Button
 */
class Save implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save'
            ],
            'sort_order' => 50,
        ];
    }
}
