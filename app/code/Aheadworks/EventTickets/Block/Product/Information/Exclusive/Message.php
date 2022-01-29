<?php
namespace Aheadworks\EventTickets\Block\Product\Information\Exclusive;

use Aheadworks\EventTickets\Block\Information\BarMessageInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Message\Builder;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface as ProductTypesConfigInterface;
use Magento\Framework\Locale\FormatInterface as LocaleFormatInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Message
 * @package Aheadworks\EventTickets\Block\Product\Information\Exclusive
 */
class Message extends View implements BarMessageInterface
{
    /**
     * @var Builder
     */
    private $builder;

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
     * @param Builder $builder
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
        Builder $builder,
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
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function canShow()
    {
        return $this->getProduct()->getExtensionAttributes()->getAwEtExclusiveProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->builder->build();
    }
}
