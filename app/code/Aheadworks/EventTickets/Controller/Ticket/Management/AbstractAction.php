<?php
namespace Aheadworks\EventTickets\Controller\Ticket\Management;

use Aheadworks\EventTickets\Api\TicketActionManagementInterface;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Controller\Ticket\Management;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Url;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\EventTickets\Controller\Ticket\Management
 */
abstract class AbstractAction extends Management
{
    /**
     * @var TicketActionManagementInterface
     */
    protected $ticketActionService;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param TicketRepositoryInterface $ticketRepository
     * @param Config $config
     * @param Url\ParamEncryptor $encryptor
     * @param Url $url
     * @param TicketActionManagementInterface $ticketActionService
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        TicketRepositoryInterface $ticketRepository,
        Config $config,
        Url\ParamEncryptor $encryptor,
        Url $url,
        TicketActionManagementInterface $ticketActionService
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $productRepository,
            $ticketRepository,
            $config,
            $encryptor
        );
        $this->url = $url;
        $this->ticketActionService = $ticketActionService;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $ticketNumber = trim($this->getRequest()->getParam('ticket_number'));
        $params = [];
        if ($ticketNumber) {
            try {
                $this->performAction($ticketNumber);
                $params = ['ticket_number' => $ticketNumber];
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addExceptionMessage($e, __('The ticket no longer exists.'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('We can\'t update ticket status.'));
            }
        }

        return $resultRedirect->setUrl($this->url->getEncryptUrl('aw_event_tickets/ticket/management', $params));
    }

    /**
     * Perform action
     *
     * @param string $ticketNumber
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    abstract protected function performAction($ticketNumber);
}
