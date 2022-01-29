<?php
namespace Aheadworks\EventTickets\Block\Checkout\Cart\Item;

use Aheadworks\EventTickets\Model\Product\Configuration as EventTicketsProductConfiguration;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Product\Configuration as ProductConfig;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Pricing\PriceCurrencyInterface as PriceCurrency;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface as MessageInterpretationStrategy;
use Magento\Checkout\Block\Cart\Item\Renderer as CartItemRenderer;

/**
 * Event Ticket product items renderer
 *
 * @package Aheadworks\EventTickets\Block\Checkout\Cart\Item
 */
class Renderer extends CartItemRenderer
{
    /**
     * @var EventTicketsProductConfiguration
     */
    private $eventTicketsProductConfiguration;

    /**
     * @param Context $context
     * @param ProductConfig $productConfig
     * @param CheckoutSession $checkoutSession
     * @param ImageBuilder $imageBuilder
     * @param UrlHelper $urlHelper
     * @param MessageManager $messageManager
     * @param PriceCurrency $priceCurrency
     * @param ModuleManager $moduleManager
     * @param MessageInterpretationStrategy $messageInterpretationStrategy
     * @param EventTicketsProductConfiguration $eventTicketsProductConfiguration
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductConfig $productConfig,
        CheckoutSession $checkoutSession,
        ImageBuilder $imageBuilder,
        UrlHelper $urlHelper,
        MessageManager $messageManager,
        PriceCurrency $priceCurrency,
        ModuleManager $moduleManager,
        MessageInterpretationStrategy $messageInterpretationStrategy,
        EventTicketsProductConfiguration $eventTicketsProductConfiguration,
        $data = []
    ) {
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
        $this->eventTicketsProductConfiguration = $eventTicketsProductConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionList()
    {
        return $this->eventTicketsProductConfiguration->getOptions($this->getItem());
    }
}
