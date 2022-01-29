<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\SessionFactory as CheckoutSessionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder
    as ReorderModel;
use Magento\Framework\Controller\Result\RedirectFactory
    as ResultRedirectFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

class Controller
{
    /**
     * @var OrderLoaderInterface
     */
    private $orderLoader;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ReorderModel
     */
    private $reorderModel;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var ResultRedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var CheckoutSessionFactory
     */
    private $checkoutSessionFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $coreRegistry
     * @param ReorderModel $reorderModel
     * @param CheckoutSession $checkoutSession
     * @param ResultRedirectFactory $resultRedirectFactory
     * @param CheckoutSessionFactory $checkoutSessionFactory
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        Registry $coreRegistry,
        ReorderModel $reorderModel,
        CheckoutSession $checkoutSession,
        ResultRedirectFactory $resultRedirectFactory,
        CheckoutSessionFactory $checkoutSessionFactory
    ) {
        $this->orderLoader = $orderLoader;
        $this->coreRegistry = $coreRegistry;
        $this->reorderModel = $reorderModel;
        $this->checkoutSession = $checkoutSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $context->getMessageManager();
        $this->request = $context->getRequest();
        $this->checkoutSessionFactory = $checkoutSessionFactory;
    }

    /**
     * Action for reorder
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $result = $this->orderLoader->load($this->request);
        if ($result instanceof ResultInterface) {
            return $result;
        }
        $order = $this->coreRegistry->registry('current_order');

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $reorderOutput = $this->reorderModel->execute(
                $order->getIncrementId(),
                $order->getStoreId()
            );
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            return $resultRedirect->setPath('checkout/cart');
        }

        // Set quote id for guest session: \Magento\Quote\Api\CartRepositoryInterface::save doesn't set quote id
        // to session for guest customer, as it does \Magento\Checkout\Model\Cart::save which is deprecated.
        $this->checkoutSession->setQuoteId($reorderOutput->getCart()->getId());

        $errors = $reorderOutput->getErrors();
        if (!empty($errors)) {
            $useNotice = $this->checkoutSessionFactory
                ->create()
                ->getUseNotice(true)
            ;
            foreach ($errors as $error) {
                $useNotice
                    ? $this->messageManager->addNoticeMessage($error->getMessage())
                    : $this->messageManager->addErrorMessage($error->getMessage());
            }
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
