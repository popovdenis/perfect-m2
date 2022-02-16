<?php

namespace Perfect\Event\Block\Adminhtml\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Dummy
 *
 * @package Perfect\Event\Block\Adminhtml\Edit\Buttons
 */
class Dummy extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get button attributes
     *
     * @return array
     */
    public function getButtonData()
    {
        return [];
    }
}