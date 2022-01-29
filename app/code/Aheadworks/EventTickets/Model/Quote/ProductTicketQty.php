<?php
namespace Aheadworks\EventTickets\Model\Quote;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

/**
 * Class ProductTicketQty
 */
class ProductTicketQty
{
    /**
     * @var EventTicket
     */
    private $eventTicket;

    /**
     * ProductTicketQty constructor.
     * @param EventTicket $eventTicket
     */
    public function __construct(
        EventTicket $eventTicket
    ) {
        $this->eventTicket = $eventTicket;
    }

    /**
     * Returns product tickets qty in quote
     *
     * @param Product $product
     * @param Quote $quote
     * @return int
     */
    public function getTicketQtyInQuote($product, $quote)
    {
        $qty = 0;
        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            /** @var Product $itemProduct */
            $itemProduct = $item->getProduct();
            if ($product && $itemProduct &&  $product->getId() == $itemProduct->getId()) {
                $qty += $item->getQty();
            }
        }

        return $qty;
    }

    /**
     * Returns product ticket qty in add product request
     *
     * @param Product $product
     * @param DataObject $request
     * @return int
     */
    public function getTicketQtyInAddProductRequest($product, $request)
    {
        if ($this->isProductAddingFromProductPage($request)) {
            $qty = $this->getProductPageRequestTicketsQty($product, $request);
        } else {
            $qty = $request->getQty();
        }

        return $qty;
    }

    /**
     * Checks if product adding from product page(AwEtSlots or AwEtTickets specified)
     *
     * @param DataObject $request
     * @return bool
     */
    private function isProductAddingFromProductPage($request)
    {
        return $request->getAwEtSlots() || $request->getAwEtTickets();
    }

    /**
     * Calculates ticket qty from product page request
     *
     * @param Product $product
     * @param DataObject $request
     * @return int
     */
    private function getProductPageRequestTicketsQty($product, $request)
    {
        $tickets = [];
        if ($this->eventTicket->isRecurring($product)) {
            $slots = $request->getAwEtSlots();
            if (is_array($slots)) {
                foreach ($slots as $slot) {
                    $slotTickets = $slot[OptionInterface::BUY_REQUEST_AW_ET_TICKETS];
                    $tickets = array_merge($tickets, is_array($slotTickets) ? $slotTickets : []);
                }
            }
        } else {
            $requestTickets =  $request->getAwEtTickets();
            $tickets = array_merge($tickets, is_array($requestTickets) ? $requestTickets : []);
        }

        $qty = 0;

        foreach ($tickets as $ticket) {
            if (!empty($ticket['qty'])) {
                $qty += $ticket['qty'];
            }
        }

        return $qty;
    }
}
