<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Ticket\Listing\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class Back
 * @package Aheadworks\EventTickets\Block\Adminhtml\Ticket\Listing\Button
 */
class Back implements ButtonProviderInterface
{
    const BACK_URL_ONE_TIME = '*/event/index';
    const BACK_URL_RECURRING = '*/recurring_event/index';

    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $slotId = $this->context->getRequest()->getParam('slot_id');
        $backUrl = $slotId ? self::BACK_URL_RECURRING : self::BACK_URL_ONE_TIME;

        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl($backUrl)),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
