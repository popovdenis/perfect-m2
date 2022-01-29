<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Event;

use Magento\Backend\App\Action\Context;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket as EventTicketProductType;
use Magento\Catalog\Model\ProductFactory;
use Magento\Backend\App\Action;

/**
 * Class NewAction
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Recurring\Event
 */
class NewAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::recurring_events';

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @param Context $context
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->_getSession()->setAwEtPageToReturn('aw_et_recurring_event_index');
        $this->_getSession()->setIsNeedToRedirectToProductGrid(false);

        return $this->_redirect(
            'catalog/product/new',
            [
                'set' => $this->productFactory->create()->getDefaultAttributeSetId(),
                'type' => EventTicketProductType::TYPE_CODE
            ]
        );
    }
}
