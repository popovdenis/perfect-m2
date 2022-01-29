<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order\Item as SalesOrderItem;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType\DefaultOperation\Processor
    as DefaultReorderOperationProcessor;
use Aheadworks\EventTickets\Api\Data\OptionInterface;

class AwEventTicket implements OperationInterface
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
        $info = $this->getPreparedRequestData($orderItemList);

        $orderItem = reset($orderItemList);
        $productToProcess = clone $product;

        return $this->defaultReorderOperationProcessor->addProductToCart(
            $cart,
            $productToProcess,
            $info,
            $orderItem
        );
    }

    /**
     * Prepare info data object to add event ticket to the cart
     *
     * @param SalesOrderItem[] $orderItemList
     * @return DataObject
     */
    protected function getPreparedRequestData(array $orderItemList)
    {
        $firstOrderItem = array_shift($orderItemList);
        $infoBuyRequest = $firstOrderItem->getProductOptionByCode('info_buyRequest');

        unset($infoBuyRequest['qty']);
        $orderItemAwEtTicketsData = $infoBuyRequest['aw_et_tickets'] ?? [];
        foreach ($orderItemAwEtTicketsData as &$awEtTicketData) {
            unset($awEtTicketData[OptionInterface::BUY_REQUEST_CONFIGURED]);
        }
        unset($awEtTicketData);
        $infoBuyRequest['aw_et_tickets'] = $orderItemAwEtTicketsData;

        /** @var DataObject $info */
        $info = $this->dataObjectFactory->create(
            [
                'data' => $infoBuyRequest,
            ]
        );

        foreach ($orderItemList as $orderItem) {
            $orderItemBuyRequestData = $orderItem->getProductOptionByCode('info_buyRequest');

            $orderItemAwEtTicketsData = $orderItemBuyRequestData['aw_et_tickets'] ?? [];
            foreach ($orderItemAwEtTicketsData as &$awEtTicketData) {
                unset($awEtTicketData[OptionInterface::BUY_REQUEST_CONFIGURED]);
            }
            unset($awEtTicketData);
            if ($orderItemAwEtTicketsData) {
                $currentAwEtTicketsData = $info->getData('aw_et_tickets');
                if (is_array($currentAwEtTicketsData)) {
                    $awEtTicketsData = array_merge(
                        $currentAwEtTicketsData,
                        $orderItemAwEtTicketsData
                    );
                    $info->setData('aw_et_tickets', $awEtTicketsData);
                }
            }

            $orderItemAwEtSlotsData = $orderItemBuyRequestData['aw_et_slots'] ?? [];
            if ($orderItemAwEtSlotsData) {
                $currentAwEtSlotsData = $info->getData('aw_et_slots');
                if (is_array($currentAwEtSlotsData)) {
                    $awEtSlotsData = array_merge(
                        $currentAwEtSlotsData,
                        $orderItemAwEtSlotsData
                    );
                    $info->setData('aw_et_slots', $awEtSlotsData);
                }
            }
        }

        return $info;
    }
}
