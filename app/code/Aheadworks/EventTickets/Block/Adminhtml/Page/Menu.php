<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;

/**
 * Class Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Page
 */
class Menu extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_EventTickets::page/menu.phtml';
}
