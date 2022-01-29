<?php
namespace Aheadworks\EventTickets\Block\Order\Item\Email;

use Magento\Framework\View\Element\Template\Context;
use \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;
use Aheadworks\EventTickets\Model\Product\Option\Render as OptionRender;

/**
 * Class Renderer
 *
 * @package Aheadworks\EventTickets\Block\Order\Item\Email
 */
class Renderer extends DefaultOrder
{
    /**
     * @var OptionRender
     */
    private $optionRender;

    /**
     * @param Context $context
     * @param OptionRender $optionRender
     * @param array $data
     */
    public function __construct(
        Context $context,
        OptionRender $optionRender,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->optionRender = $optionRender;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemOptions()
    {
        return $this->optionRender->render(
            $this->getItem()->getProductOptions(),
            null,
            OptionRender::FRONTEND_SECTION
        );
    }
}
