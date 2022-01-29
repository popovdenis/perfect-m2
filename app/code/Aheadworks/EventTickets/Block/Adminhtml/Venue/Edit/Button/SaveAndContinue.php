<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Venue\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinue
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Venue\Edit\Button
 */
class SaveAndContinue implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 40,
        ];
    }
}
