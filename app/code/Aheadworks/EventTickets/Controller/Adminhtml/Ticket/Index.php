<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Ticket;

use Magento\Framework\Phrase;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\EventTickets\Model\Ticket\Grid\TitleResolver;
use Magento\Backend\App\Action;

/**
 * Class Index
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Ticket
 */
class Index extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::tickets';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var TitleResolver
     */
    private $titleResolver;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param TitleResolver $titleResolver
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        TitleResolver $titleResolver
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->titleResolver = $titleResolver;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_EventTickets::events');
        $resultPage->getConfig()->getTitle()->prepend($this->getTitleForTicketsList());

        return $resultPage;
    }

    /**
     * Retrieve title for tickets list grid
     *
     * @return Phrase
     */
    private function getTitleForTicketsList()
    {
        $eventProduct = $this->getRelatedEventProduct();
        $productDescription = $eventProduct
            ? $this->titleResolver->getEventProductDescription($eventProduct)
            : '';

        return __('Tickets to %1', $productDescription);
    }

    /**
     * Retrieve current event product
     *
     * @return ProductInterface|null
     */
    private function getRelatedEventProduct()
    {
        $eventProduct = null;
        $eventProductId = $this->getRequest()->getParam('product_id');
        try {
            $eventProduct = $this->productRepository->getById($eventProductId);
        } catch (\Exception $exception) {
        }
        return $eventProduct;
    }
}
