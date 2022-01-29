<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View;

use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\Config;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;

/**
 * Class Provider
 *
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View
 */
class Provider implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var EventTicket
     */
    private $eventTicket;

    /**
     * @param ArrayManager $arrayManager
     * @param FormatInterface $localeFormat
     * @param Config $config
     * @param EventTicket $eventTicket
     */
    public function __construct(
        ArrayManager $arrayManager,
        FormatInterface $localeFormat,
        Config $config,
        EventTicket $eventTicket
    ) {
        $this->arrayManager = $arrayManager;
        $this->localeFormat = $localeFormat;
        $this->config = $config;
        $this->eventTicket = $eventTicket;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout, $product)
    {
        $optionsProviderPath = 'components/awEtViewOptionsProvider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            [
                'data' => [
                    'priceFormat' => $this->localeFormat->getPriceFormat(),
                    'displayTaxes' => $this->config->getValue(
                        TaxConfig::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        ScopeInterface::SCOPE_STORE
                    ),
                    'isRecurring' => $this->eventTicket->isRecurring($product)
                ]
            ]
        );

        return $jsLayout;
    }
}
