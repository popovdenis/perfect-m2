<?php
namespace Aheadworks\EventTickets\Block\Product;

use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\LayoutProcessorInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Catalog\Model\Product;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface as ProductTypesConfigInterface;
use Magento\Framework\Locale\FormatInterface as LocaleFormatInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class View
 *
 * @package Aheadworks\EventTickets\Block\Product\View
 */
class View extends \Magento\Catalog\Block\Product\View implements IdentityInterface
{
    /**
     * @var LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * @param ProductContext $context
     * @param UrlEncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param ProductHelper $productHelper
     * @param ProductTypesConfigInterface $productTypeConfig
     * @param LocaleFormatInterface $localeFormat
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        ProductContext $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        ProductHelper $productHelper,
        ProductTypesConfigInterface $productTypeConfig,
        LocaleFormatInterface $localeFormat,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
        $this->layoutProcessors = $layoutProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout, $this->getProduct());
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [Product::CACHE_TAG . '_' . $this->getProduct()->getId()];
    }

    /**
     * Escape special symbols
     *
     * @param string $json
     * @return string
     */
    public function escapeSpecialSymbols($json)
    {
        $json = str_replace("'", '\u0027', $json);
        $json = str_replace('&quot;', '\"', $json);
        return $json;
    }

    /**
     * Get recurring type id
     */
    public function getRecurringType()
    {
        return ScheduleType::RECURRING;
    }

    /**
     * Get filter flag
     */
    public function getFilterFlag()
    {
        $recurringSchedule = $this->getProduct()->getExtensionAttributes()->getAwEtRecurringSchedule();
        return $recurringSchedule->getFilterByTicketQty();
    }
}
