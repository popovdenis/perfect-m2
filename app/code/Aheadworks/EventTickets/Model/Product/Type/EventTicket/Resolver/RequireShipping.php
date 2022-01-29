<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver;

use Aheadworks\EventTickets\Model\Config;
use Magento\Catalog\Model\Product;

/**
 * Class RequireShipping
 *
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver
 */
class RequireShipping
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Resolve value
     *
     * @param Product $product
     * @return bool
     */
    public function resolve($product)
    {
        $requireShipping = $product->getTypeInstance()->isRequireShipping($product);
        if (null === $requireShipping) {
            $requireShipping = $this->config->isTicketRequireShipping();
        }

        return $requireShipping;
    }
}
