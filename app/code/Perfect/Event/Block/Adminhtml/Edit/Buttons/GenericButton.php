<?php

namespace Perfect\Event\Block\Adminhtml\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 *
 * @package Perfect\Event\Block\Adminhtml\Edit\Buttons
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * GenericButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array  $params
     *
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
