<?php
namespace Aheadworks\EventTickets\Plugin\Block\Wishlist;

use Magento\Framework\View\Element\Template;

/**
 * Class Plugin
 *
 * @package Aheadworks\EventTickets\Plugin\Block\Wishlist
 */
class OptionsPlugin
{
    /**
     * Add Event Ticket options to wishlist widget
     *
     * @param Template $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetWishlistOptions(Template $subject, $result)
    {
        return array_merge($result, ['aw_event_ticketInfo' => '[name^=aw_et_]']);
    }
}
