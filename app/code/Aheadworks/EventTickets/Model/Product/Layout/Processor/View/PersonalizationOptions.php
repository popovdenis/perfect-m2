<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class PersonalizationOptions
 *
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View
 */
class PersonalizationOptions
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $product)
    {
        $optionsProviderPath = 'components/awEtViewOptionsProvider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            ['data' => $this->getData($product)]
        );

        return $jsLayout;
    }

    /**
     * Retrieve data
     *
     * @param Product $product
     * @return array
     */
    private function getData($product)
    {
        return array_merge(
            ['customOptions' => $this->getPreparedOptions($product)],
            $this->getPreconfiguredAttendeeValues($product)
        );
    }

    /**
     * Retrieve prepared product options
     *
     * @param Product $product
     * @return array
     */
    private function getPreparedOptions($product)
    {
        $preparedOptions = [];
        /** @var ProductPersonalOptionInterface[] $options */
        $options = $product->getTypeInstance()->getPersonalOptions($product);
        foreach ($options as $option) {
            $preparedOptions[] = [
                'id' => $option->getId(),
                'uid' => $option->getUid(),
                'type' => $option->getType(),
                'label' => $option->getCurrentLabels()->getTitle(),
                'isRequire' => $option->isRequire(),
                'options' => $this->resolveOptionsForProductOption($option)
            ];
        }

        return $preparedOptions;
    }

    /**
     * Resolve options for specified product option
     *
     * @param ProductPersonalOptionInterface $productOption
     * @return array|null
     */
    private function resolveOptionsForProductOption($productOption)
    {
        $options = null;
        if ($productOption->getType() == ProductPersonalOptionInterface::OPTION_TYPE_DROPDOWN) {
            $options = $this->preparePersonalOptionValues($productOption->getValues());
        }
        return $options;
    }

    /**
     * Retrieve prepared product option values
     *
     * @param ProductPersonalOptionValueInterface[] $values
     * @return array
     */
    private function preparePersonalOptionValues($values)
    {
        $preparedValues = [];
        if (empty($values)) {
            return $preparedValues;
        }

        foreach ($values as $value) {
            $preparedValues[] = [
                'value' => $value->getId(),
                'label' => $value->getCurrentLabels()->getTitle()
            ];
        }

        return $preparedValues;
    }

    /**
     * Retrieve preconfigured attendee values
     *
     * @param Product $product
     * @return array
     */
    private function getPreconfiguredAttendeeValues($product)
    {
        $attendee = $product->getPreconfiguredValues()->getData(OptionInterface::BUY_REQUEST_ATTENDEE);
        if (empty($attendee)) {
            return [];
        }

        $tickets = [
            [OptionInterface::BUY_REQUEST_ATTENDEE => $attendee]
        ];

        return [
            OptionInterface::BUY_REQUEST_AW_ET_TICKETS => $tickets,
            OptionInterface::BUY_REQUEST_AW_ET_SLOTS => [
                [OptionInterface::BUY_REQUEST_AW_ET_TICKETS => $tickets]
            ]
        ];
    }
}
