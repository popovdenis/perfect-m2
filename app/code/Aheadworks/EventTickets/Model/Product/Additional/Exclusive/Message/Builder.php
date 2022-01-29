<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Message;

use Aheadworks\EventTickets\Model\Config;
use Magento\Framework\UrlInterface;

/**
 * Class Builder
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Message
 */
class Builder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Config $config
     * @param UrlInterface $urlBuilder
     */
    public function __construct(Config $config, UrlInterface $urlBuilder)
    {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Build exclusive message
     *
     * @return \Magento\Framework\Phrase
     */
    public function build()
    {
        $url = $this->config->getUrlToEventsCategory();
        $message = $url
            ? __(
                'This product can be purchased only along with a ticket. Click <a href="%1">here</a> to select one.',
                $this->urlBuilder->getUrl($url)
            )
            : $this->buildShort();

        return $message;
    }

    /**
     * Build short exclusive message
     *
     * @return \Magento\Framework\Phrase
     */
    public function buildShort()
    {
        return __('This product can be purchased only along with a ticket.');
    }
}
