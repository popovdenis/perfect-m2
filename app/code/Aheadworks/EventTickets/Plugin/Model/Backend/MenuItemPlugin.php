<?php
namespace Aheadworks\EventTickets\Plugin\Model\Backend;

use Magento\Backend\Model\UrlInterface;
use Magento\Backend\Model\Menu\Item;

/**
 * Class MenuItemPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Model\Backend
 */
class MenuItemPlugin
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * Add menu param to menu item url
     *
     * @param Item $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetUrl(Item $subject, \Closure $proceed)
    {
        if ($subject->getAction() == 'catalog/product/') {
            return $this->url->getUrl(
                (string)$subject->getAction(),
                ['_cache_secret_key' => true, 'menu' => true]
            );
        }
        return $proceed();
    }
}
