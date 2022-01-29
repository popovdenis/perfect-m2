<?php
namespace Aheadworks\EventTickets\Model\Product;

use Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Aheadworks\EventTickets\Model\Product\Option\Render as OptionRender;

/**
 * Class Configuration
 *
 * @package Aheadworks\EventTickets\Model\Product
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var OptionRender
     */
    private $optionRender;

    /**
     * @param OptionRender $optionRender
     */
    public function __construct(
        OptionRender $optionRender
    ) {
        $this->optionRender = $optionRender;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(ItemInterface $item)
    {
        $options = [];
        if (is_array($item->getOptionsByCode())) {
            /** @var \Magento\Quote\Model\Quote\Item\Option $option */
            foreach ($item->getOptionsByCode() as $option) {
                $options[$option->getCode()] = $option->getValue();
            }
        }
        return $this->optionRender->render($options, $item->getProduct(), OptionRender::FRONTEND_SECTION);
    }
}
