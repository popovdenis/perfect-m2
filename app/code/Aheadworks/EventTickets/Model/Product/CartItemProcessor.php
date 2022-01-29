<?php
namespace Aheadworks\EventTickets\Model\Product;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;
use Magento\Quote\Api\Data\ProductOptionExtensionFactory;

/**
 * Class CartItemProcessor
 *
 * @package Aheadworks\EventTickets\Model\Product
 */
class CartItemProcessor implements CartItemProcessorInterface
{
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory
     */
    private $extensionFactory;

    /**
     * @param DataObjectFactory $objectFactory
     * @param OptionExtractor $optionExtractor
     * @param ProductOptionInterfaceFactory $productOptionFactory
     * @param ProductOptionExtensionFactory $extensionFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        OptionExtractor $optionExtractor,
        ProductOptionInterfaceFactory $productOptionFactory,
        ProductOptionExtensionFactory $extensionFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->optionExtractor = $optionExtractor;
        $this->productOptionFactory = $productOptionFactory;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToBuyRequest(CartItemInterface $cartItem)
    {
        $productOptions = $cartItem->getProductOption();
        if ($productOptions && $productOptions->getExtensionAttributes()
            && $productOptions->getExtensionAttributes()->getAwEtOption()
        ) {
            $arrayOptions = $this->optionExtractor
                ->extractFromObject($productOptions->getExtensionAttributes()->getAwEtOption());
            if (!is_array($arrayOptions)) {
                return null;
            }
            $requestData = [];
            foreach ($arrayOptions as $key => $value) {
                $requestData[$key] = $value;
            }
            return $this->objectFactory->create($requestData);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        $productOptions = [];
        $options = $cartItem->getOptions();
        if (!is_array($options)) {
            return $cartItem;
        };

        /** @var \Magento\Quote\Model\Quote\Item\Option $option */
        foreach ($options as $option) {
            $productOptions[$option->getCode()] = $option->getValue();
        }
        $objectOption = $this->optionExtractor->extractFromArray($productOptions, $cartItem->getProduct());

        $productOption = ($cartItem->getProductOption())
            ? $cartItem->getProductOption()
            : $this->productOptionFactory->create();

        $extensibleAttribute =  ($productOption->getExtensionAttributes())
            ? $productOption->getExtensionAttributes()
            : $this->extensionFactory->create();

        $extensibleAttribute->setAwEtOption($objectOption);
        $productOption->setExtensionAttributes($extensibleAttribute);
        $cartItem->setProductOption($productOption);

        return $cartItem;
    }
}
