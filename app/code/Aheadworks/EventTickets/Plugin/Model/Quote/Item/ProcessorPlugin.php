<?php
namespace Aheadworks\EventTickets\Plugin\Model\Quote\Item;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item;

/**
 * Class ProcessorPlugin
 * @package Aheadworks\EventTickets\Plugin\Model\Quote\Item
 */
class ProcessorPlugin
{
    /**
     * Set promo params to quote item if needed
     *
     * @param Item\Processor $subject
     * @param \Closure $proceed
     * @param Item $item
     * @param DataObject $request
     * @param Product $candidate
     * @return void
     */
    public function aroundPrepare($subject, \Closure $proceed, $item, $request, $candidate)
    {
        $proceed($item, $request, $candidate);
        if ($request->getAwUniqueId()) {
            $item->setAwUniqueId($request->getAwUniqueId());
        }
    }
}
