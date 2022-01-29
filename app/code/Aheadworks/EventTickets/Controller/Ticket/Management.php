<?php
namespace Aheadworks\EventTickets\Controller\Ticket;

use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Url\ParamEncryptor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Management
 *
 * @package Aheadworks\EventTickets\Controller\Ticket
 */
class Management extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ParamEncryptor
     */
    private $encryptor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param TicketRepositoryInterface $ticketRepository
     * @param Config $config
     * @param ParamEncryptor $encryptor
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        TicketRepositoryInterface $ticketRepository,
        Config $config,
        ParamEncryptor $encryptor
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->ticketRepository = $ticketRepository;
        $this->config = $config;
        $this->encryptor = $encryptor;
    }

    /**
     * Check if admin authenticate
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate() || !$this->isAllowedCustomerGroup()) {
            if ($productUrl = $this->getProductUrl()) {
                return $this->redirectToUrl($productUrl);
            }
            throw new NotFoundException(__('Page not found.'));
        }

        if ($this->isCheckInTicket()) {
            if ($checkInTicketUrl = $this->getCheckInTicketUrl()) {
                return $this->redirectToUrl($checkInTicketUrl);
            }
        }

        return parent::dispatch($request);
    }

    /**
     * Ticket management action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var $resultPage \Magento\Framework\View\Result\Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Ticket Management'));

        return $resultPage;
    }

    /**
     * Redirect to url
     *
     * @param string $url
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function redirectToUrl($url)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($url);
        return $resultRedirect;
    }

    /**
     * Check if customer group is allowed
     *
     * @return bool
     */
    protected function isAllowedCustomerGroup()
    {
        return $this->customerSession->getCustomerGroupId() == $this->config->getTicketManagementGroupOnStorefront();
    }

    /**
     * Retrieve product url
     *
     * @return string|false
     */
    protected function getProductUrl()
    {
        try {
            $ticket = $this->ticketRepository->get($this->getTicketNumber());
            /** @var Product|ProductInterface $product */
            $product = $this->productRepository->getById($ticket->getProductId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        if ($this->hasProductUrl($product)) {
            return $product->getUrlModel()->getUrl($product, ['_escape' => true]);
        }

        return false;
    }

    /**
     * Check if check in ticket
     *
     * @return bool
     */
    private function isCheckInTicket()
    {
        return (bool)$this->encryptor->decrypt('checkIn', $this->getRequest()->getParam('key'));
    }

    /**
     * Retrieve check in ticket url
     *
     * @return string|bool
     */
    private function getCheckInTicketUrl()
    {
        $ticketNumber = $this->getTicketNumber();
        if (empty($ticketNumber)) {
            return false;
        }

        return $this->_url->getUrl('aw_event_tickets/ticket/management_checkIn', ['ticket_number' => $ticketNumber]);
    }

    /**
     * Retrieve ticket number from url
     *
     * @return string|bool
     */
    private function getTicketNumber()
    {
        return trim($this->encryptor->decrypt('ticket_number', $this->getRequest()->getParam('key')));
    }

    /**
     * Check Product has URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }
}
