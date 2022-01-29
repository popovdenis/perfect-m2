<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Api\CartManagementInterface;
use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface;
use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterfaceFactory;
use Aheadworks\EventTickets\Model\Product\Additional\PostDataProcessor\Composite as PostDataProcessor;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class AddAdditionalProductToCart
 * @package Aheadworks\EventTickets\Observer
 */
class AddAdditionalProductToCartObserver implements ObserverInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @var AdditionalProductOptionsInterfaceFactory
     */
    private $additionalProductOptionsFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CartManagementInterface $cartManagement
     * @param CheckoutSession $checkoutSession
     * @param PostDataProcessor $postDataProcessor
     * @param AdditionalProductOptionsInterfaceFactory $additionalProductOptionsFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CartManagementInterface $cartManagement,
        CheckoutSession $checkoutSession,
        PostDataProcessor $postDataProcessor,
        AdditionalProductOptionsInterfaceFactory $additionalProductOptionsFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->cartManagement = $cartManagement;
        $this->checkoutSession = $checkoutSession;
        $this->postDataProcessor = $postDataProcessor;
        $this->additionalProductOptionsFactory = $additionalProductOptionsFactory;
    }

    /**
     * Add additional product to cart
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        /** @var Product|ProductInterface $product */
        $product = $observer->getEvent()->getProduct();
        $quote = $this->checkoutSession->getQuote();

        if ($product->getTypeId() == EventTicket::TYPE_CODE) {
            $additionalProductsOptions = [];
            $awEtProducts = $this->prepareProducts($request);
            foreach ($awEtProducts as $product) {
                if (isset($product[AdditionalProductOptionsInterface::QTY])
                    && $product[AdditionalProductOptionsInterface::QTY] > 0
                ) {
                    $preparedAdditionalProductOptions = $this->postDataProcessor->prepareEntityData($product);
                    $additionalProductOptions = $this->additionalProductOptionsFactory->create();
                    $this->dataObjectHelper->populateWithArray(
                        $additionalProductOptions,
                        $preparedAdditionalProductOptions,
                        AdditionalProductOptionsInterface::class
                    );
                    $additionalProductsOptions[] = $additionalProductOptions;
                }
            }
            $this->cartManagement->addRelatedProducts($quote->getId(), $additionalProductsOptions);
        }
    }

    /**
     * Prepare products
     *
     * @param RequestInterface $request
     * @return array
     */
    private function prepareProducts($request)
    {
        $awEtProducts = $request->getParam('aw_et_products', []);
        if (empty($awEtProducts)) {
            $slots = $request->getParam('aw_et_slots', []);
            foreach ($slots as $slot) {
                if (isset($slot['aw_et_products']) && !empty($slot['aw_et_products'])) {
                    foreach ($slot['aw_et_products'] as $product) {
                        $awEtProducts[] = $product;
                    }
                }

            }
        }

        return $awEtProducts;
    }
}
