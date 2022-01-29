<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\EventTickets\Model\Quote\Cart\Resolver\Factory as CartResolverFactory;
use Magento\Sales\Helper\Reorder as ReorderHelper;
use Magento\Sales\Model\OrderFactory;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\ReorderOutput;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data\Error as ReorderDataError;
use Magento\Framework\Exception\AlreadyExistsException;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Product\Resolver as ProductResolver;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation as ReorderOperation;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\Error\Generator as ErrorGenerator;

class Reorder
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var ReorderHelper
     */
    private $reorderHelper;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ReorderDataError[]
     */
    private $errors = [];

    /**
     * @var CartResolverFactory
     */
    private $cartResolverFactory;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var ReorderOperation
     */
    private $reorderOperation;

    /**
     * @var ErrorGenerator
     */
    private $errorGenerator;

    /**
     * @param OrderFactory $orderFactory
     * @param CartResolverFactory $cartResolverFactory
     * @param CartRepositoryInterface $cartRepository
     * @param ReorderHelper $reorderHelper
     * @param ProductResolver $productResolver
     * @param ReorderOperation $reorderOperation
     * @param ErrorGenerator $errorGenerator
     */
    public function __construct(
        OrderFactory $orderFactory,
        CartResolverFactory $cartResolverFactory,
        CartRepositoryInterface $cartRepository,
        ReorderHelper $reorderHelper,
        ProductResolver $productResolver,
        ReorderOperation $reorderOperation,
        ErrorGenerator $errorGenerator
    ) {
        $this->orderFactory = $orderFactory;
        $this->cartRepository = $cartRepository;
        $this->reorderHelper = $reorderHelper;
        $this->cartResolverFactory = $cartResolverFactory;
        $this->productResolver = $productResolver;
        $this->reorderOperation = $reorderOperation;
        $this->errorGenerator = $errorGenerator;
    }

    /**
     * Allows customer quickly to reorder previously added products and put them to the Cart
     *
     * @param string $orderNumber
     * @param string $storeId
     * @return ReorderOutput
     * @throws InputException Order is not found
     * @throws NoSuchEntityException The specified customer does not exist.
     * @throws CouldNotSaveException Could not create customer Cart
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function execute(string $orderNumber, string $storeId): ReorderOutput
    {
        $order = $this->orderFactory->create()->loadByIncrementIdAndStoreId($orderNumber, $storeId);

        if (!$order->getId()) {
            throw new InputException(
                __('Cannot find order number "%1" in store "%2"', $orderNumber, $storeId)
            );
        }
        $customerId = (int)$order->getCustomerId();
        $this->errors = [];

        $cartResolver = $this->cartResolverFactory->getInstance();
        if ($this->isGuestCustomer($customerId)) {
            $cart = $cartResolver->getForGuest();
        } else {
            $cart = $cartResolver->getForCustomer($customerId);
        }
        if (!$this->reorderHelper->isAllowed($order->getStore())) {
            $this->errors[] = $this->errorGenerator->createByMessage(
                (string)__('Reorders are not allowed.'),
                ErrorGenerator::ERROR_REORDER_NOT_AVAILABLE
            );
            return $this->prepareOutput($cart);
        }

        $orderItemList = $order->getItemsCollection()->getItems();
        $unavailableProductIdList = $this->productResolver->getUnavailableProductIdList(
            $orderItemList,
            $storeId
        );
        $this->processUnavailableProductIdList($unavailableProductIdList);

        $errorList = $this->reorderOperation->addOrderItemListToCart(
            $cart,
            $storeId,
            $orderItemList
        );

        $this->errors = array_merge($this->errors, $errorList);

        try {
            $this->cartRepository->save($cart);
        } catch (LocalizedException $e) {
            // handle exception from \Magento\Quote\Model\QuoteRepository\SaveHandler::save
            $this->errors[] = $this->errorGenerator->createByMessage(
                $e->getMessage()
            );
        }

        $savedCart = $this->cartRepository->get($cart->getId());

        return $this->prepareOutput($savedCart);
    }

    /**
     * Process the list of unavailable product id
     *
     * @param int[] $unavailableProductIdList
     * @return $this
     */
    protected function processUnavailableProductIdList($unavailableProductIdList)
    {
        if (!empty($unavailableProductIdList)) {
            foreach ($unavailableProductIdList as $productId) {
                $this->errors[] = $this->errorGenerator->createByMessage(
                    (string)__('Could not find a product with ID "%1"', $productId),
                    ErrorGenerator::ERROR_PRODUCT_NOT_FOUND
                );
            }
        }
        return $this;
    }

    /**
     * Prepare output
     *
     * @param CartInterface $cart
     * @return ReorderOutput
     */
    protected function prepareOutput(CartInterface $cart): ReorderOutput
    {
        $output = new ReorderOutput($cart, $this->errors);
        $this->errors = [];
        // we already show user errors, do not expose it to cart level
        $cart->setHasError(false);
        return $output;
    }

    /**
     * Check if customer with given id is guest
     *
     * @param int $customerId
     * @return bool
     */
    protected function isGuestCustomer(int $customerId)
    {
        return $customerId === 0;
    }
}