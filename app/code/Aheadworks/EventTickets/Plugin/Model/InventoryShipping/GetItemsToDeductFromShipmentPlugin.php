<?php
namespace Aheadworks\EventTickets\Plugin\Model\InventoryShipping;

use Magento\Sales\Model\Order\Shipment;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;

/**
 * Class GetItemsToDeductFromShipmentPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Model\InventoryShipping
 */
class GetItemsToDeductFromShipmentPlugin
{
    /**
     * Remove event ticket items from result array
     *
     * @param \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment $subject
     * @param array $resultItems
     * @param Shipment $shipment
     * @return mixed
     */
    public function afterExecute(
        \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment $subject,
        $resultItems,
        Shipment $shipment
    ) {
        /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */
        foreach ($shipment->getAllItems() as $shipmentItem) {
            $orderItem = $shipmentItem->getOrderItem();
            if (null === $orderItem) {
                continue;
            }
            if ($orderItem->getProductType() == EventTicket::TYPE_CODE) {
                foreach ($resultItems as $index => $itemToDeduct) {
                    if ($orderItem->getSku() == $itemToDeduct->getSku()) {
                        unset($resultItems[$index]);
                    }
                }
            }
        }
        return $resultItems;
    }
}
