<?php
namespace Aheadworks\EventTickets\Model\Product\Option;

use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class Processor
 *
 * @package Aheadworks\EventTickets\Model\Product\Option
 */
class Processor implements ProductOptionProcessorInterface
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
     * @param DataObjectFactory $objectFactory
     * @param OptionExtractor $optionExtractor
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        OptionExtractor $optionExtractor
    ) {
        $this->objectFactory = $objectFactory;
        $this->optionExtractor = $optionExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $etOptions = $this->getEventTicketOptions($productOption);
        if (!empty($etOptions)) {
            $request->addData($etOptions);
        }
        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToProductOption(DataObject $request)
    {
        $options = [];
        $requestOptions = $request->getData();
        foreach ($requestOptions as $optionKey => $optionValue) {
            $options[$optionKey] = $optionValue;
        }

        if (!empty($options) && is_array($options)) {
            $objectOptions = $this->optionExtractor->extractFromArray($options);
            return ['aw_et_option' => $objectOptions];
        };

        return [];
    }

    /**
     * Retrieve Event Ticket options
     *
     * @param ProductOptionInterface $productOption
     * @return array
     */
    private function getEventTicketOptions(ProductOptionInterface $productOption)
    {
        if ($productOption
            && $productOption->getExtensionAttributes()
            && $productOption->getExtensionAttributes()->getAwEtOption()
        ) {
            return $this->optionExtractor->extractFromObject($productOption->getExtensionAttributes()->getAwEtOption());
        }
        return [];
    }
}
