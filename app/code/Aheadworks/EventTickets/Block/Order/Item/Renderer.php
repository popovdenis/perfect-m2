<?php
namespace Aheadworks\EventTickets\Block\Order\Item;

use Aheadworks\EventTickets\Model\Product\Option\Render as OptionRender;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;

/**
 * Class Renderer
 *
 * @package Aheadworks\EventTickets\Block\Order\Item
 */
class Renderer extends DefaultRenderer
{
    /**
     * @var OptionRender
     */
    private $optionRender;

    /**
     * @param Context $context
     * @param StringUtils $string
     * @param OptionFactory $productOptionFactory
     * @param OptionRender $optionRender
     * @param array $data
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        OptionFactory $productOptionFactory,
        OptionRender $optionRender,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $string,
            $productOptionFactory,
            $data
        );
        $this->optionRender = $optionRender;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemOptions()
    {
        return $this->optionRender->render(
            $this->getOrderItem()->getProductOptions(),
            null,
            OptionRender::FRONTEND_SECTION
        );
    }
}
