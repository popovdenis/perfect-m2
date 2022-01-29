<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType;

use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType\DefaultOperation\Processor as DefaultReorderOperationProcessor;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote;

class DefaultOperation implements OperationInterface
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var DefaultReorderOperationProcessor
     */
    private $defaultReorderOperationProcessor;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param DefaultReorderOperationProcessor $defaultReorderOperationProcessor
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        DefaultReorderOperationProcessor $defaultReorderOperationProcessor
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->defaultReorderOperationProcessor = $defaultReorderOperationProcessor;
    }

    /**
     * @inheritdoc
     */
    public function addProductOrderItemListToCart(
        Quote $cart,
        Product $product,
        array $orderItemList
    ) {
        $errorList = [];

        foreach ($orderItemList as $orderItem) {
            $productToProcess = clone $product;

            $infoBuyRequest = $orderItem->getProductOptionByCode('info_buyRequest');
            /** @var DataObject $info */
            $info = $this->dataObjectFactory->create(
                [
                    'data' => $infoBuyRequest,
                ]
            );
            $info->setQty($orderItem->getQtyOrdered());

            $errorList[] = $this->defaultReorderOperationProcessor->addProductToCart(
                $cart,
                $productToProcess,
                $info,
                $orderItem
            );
        }

        return array_merge(...$errorList);
    }
}
