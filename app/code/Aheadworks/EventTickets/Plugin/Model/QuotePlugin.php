<?php
namespace Aheadworks\EventTickets\Plugin\Model;

use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Validator;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Quote\AddProduct\Validator\ValidatorInterface as AddProductToQuoteValidator;

/**
 * Class QuotePlugin
 * @package Aheadworks\EventTickets\Plugin\Model
 */
class QuotePlugin
{
    /**
     * @var Validator
     */
    private $exclusiveProductValidator;

    /**
     * @var AddProductToQuoteValidator
     */
    private $addProductToQuoteValidator;

    /**
     * QuotePlugin constructor.
     * @param Validator $exclusiveProductValidator
     * @param AddProductToQuoteValidator $addProductToQuoteValidator
     */
    public function __construct(
        Validator $exclusiveProductValidator,
        AddProductToQuoteValidator $addProductToQuoteValidator
    ) {
        $this->exclusiveProductValidator = $exclusiveProductValidator;
        $this->addProductToQuoteValidator = $addProductToQuoteValidator;
    }

    /**
     * Validate exclusive product after add to cart
     *
     * @param Quote $subject
     * @param Item|string $item
     * @return Item
     * @throws LocalizedException
     */
    public function afterAddProduct(
        $subject,
        $item
    ) {
        if ($item instanceof Item) {
            $this->exclusiveProductValidator->validate($item, $subject);
        }
        return $item;
    }

    /**
     * Validate before add to cart
     *
     * @param Quote $subject
     * @param Product $product
     * @param DataObject $request
     * @return null
     */
    public function beforeAddProduct(
        $subject,
        Product $product,
        $request
    ) {
        if ($product->getTypeId() == EventTicket::TYPE_CODE) {
            $this->addProductToQuoteValidator->validate($subject, $product, $request);
        }

        return null;
    }
}
