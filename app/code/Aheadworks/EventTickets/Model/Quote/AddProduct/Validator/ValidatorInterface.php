<?php
namespace Aheadworks\EventTickets\Model\Quote\AddProduct\Validator;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ValidatorInterface
 * @package Aheadworks\EventTickets\Model\Quote\AddProduct
 */
interface ValidatorInterface
{
    /**
     * Validate add product to quote
     *
     * @param Quote $quote
     * @param Product $product
     * @param DataObject $request
     * @return bool
     * @throw LocalizedException
     */
    public function validate($quote, $product, $request);
}
