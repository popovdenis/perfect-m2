<?php
namespace Aheadworks\EventTickets\Plugin\Controller\Catalog\Adminhtml\Product;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorProductInterface;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Converter as RecurringScheduleConverter;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class InitializationHelperPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Controller\Catalog\Adminhtml\Product
 */
class InitializationHelperPlugin
{
    /**
     * @var ProductSectorInterfaceFactory
     */
    private $productSectorFactory;

    /**
     * @var ProductPersonalOptionInterfaceFactory
     */
    private $productPersonalOptionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RecurringScheduleConverter
     */
    private $recurringScheduleConverter;

    /**
     * @param ProductSectorInterfaceFactory $productSectorFactory
     * @param ProductPersonalOptionInterfaceFactory $productPersonalOptionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param RecurringScheduleConverter $recurringScheduleConverter
     */
    public function __construct(
        ProductSectorInterfaceFactory $productSectorFactory,
        ProductPersonalOptionInterfaceFactory $productPersonalOptionFactory,
        DataObjectHelper $dataObjectHelper,
        RecurringScheduleConverter $recurringScheduleConverter
    ) {
        $this->productSectorFactory = $productSectorFactory;
        $this->productPersonalOptionFactory = $productPersonalOptionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->recurringScheduleConverter = $recurringScheduleConverter;
    }

    /**
     * Add event ticket extension attributes after initialize product
     *
     * @param InitializationHelper $subject
     * @param \Closure $proceed
     * @param Product $product
     * @param array $productData
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInitializeFromData(
        InitializationHelper $subject,
        \Closure $proceed,
        Product $product,
        array $productData
    ) {
        $product = $proceed($product, $productData);
        if ($product->getTypeId() != EventTicket::TYPE_CODE) {
            return $product;
        }
        $product->setData(
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
            $this->prepareProductAttribute($productData, ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG)
        );
        $product->setData(
            ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS,
            $this->prepareProductAttribute($productData, ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS)
        );

        $extension = $product->getExtensionAttributes();
        $extension
            ->setAwEtSectorConfig($this->getSectorConfigData($product))
            ->setAwEtPersonalOptions($this->getPersonalOptions($product))
            ->setAwEtRecurringSchedule($this->recurringScheduleConverter->fromFormData($productData));
        $product->setExtensionAttributes($extension);

        return $product;
    }

    /**
     * Prepare product attribute
     *
     * @param array $productData
     * @param string $attribute
     * @return array
     */
    private function prepareProductAttribute($productData, $attribute)
    {
        return isset($productData[$attribute]) ? $productData[$attribute] : [];
    }

    /**
     * Retrieve sector config data
     *
     * @param Product $product
     * @return array
     */
    private function getSectorConfigData($product)
    {
        $sectorConfigData = $product->getData(ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG);
        $data = [];
        if (!is_array($sectorConfigData)) {
            return $data;
        }
        foreach ($sectorConfigData as $sector) {
            if (!isset($sector['sector_tickets'])
                || !is_array($sector['sector_tickets'])
            ) {
                continue;
            }
            $productSectorData = ['sector_id' => $sector['sector_id']];
            foreach ($sector['sector_tickets'] as $ticket) {
                $productSectorData[ProductSectorInterface::SECTOR_TICKETS][] = [
                    ProductSectorTicketInterface::TYPE_ID => $ticket['type_id'],
                    ProductSectorTicketInterface::EARLY_BIRD_PRICE => $ticket['early_bird_price'],
                    ProductSectorTicketInterface::PRICE => $ticket['price'],
                    ProductSectorTicketInterface::LAST_DAYS_PRICE => $ticket['last_days_price'],
                    ProductSectorTicketInterface::POSITION => $ticket['position'],
                    ProductSectorTicketInterface::PERSONAL_OPTION_UIDS => isset($ticket['personal_option_uids'])
                        ? $ticket['personal_option_uids']
                        : []
                ];
            }

            if (isset($sector['sector_products'])
                && is_array($sector['sector_products'])
            ) {
                foreach ($sector['sector_products'] as $product) {
                    $productSectorData[ProductSectorInterface::SECTOR_PRODUCTS][] = [
                        ProductSectorProductInterface::PRODUCT_ID => $product['id'],
                        ProductSectorProductInterface::POSITION => $product['position'],
                    ];
                }
            }

            $productSectorDataObject = $this->productSectorFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productSectorDataObject,
                $productSectorData,
                ProductSectorInterface::class
            );
            $data[] = $productSectorDataObject;
        }
        return $data;
    }

    /**
     * Retrieve personal options data
     *
     * @param Product $product
     * @return array
     */
    private function getPersonalOptions($product)
    {
        $personalOptions = $product->getData(ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS);
        $data = [];
        if (!is_array($personalOptions)) {
            return $data;
        }
        foreach ($personalOptions as $option) {
            $this->preparePersonalOptionValues($option);
            $productPersonalOptionDataObject = $this->productPersonalOptionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productPersonalOptionDataObject,
                $option,
                ProductPersonalOptionInterface::class
            );
            $data[] = $productPersonalOptionDataObject;
        }

        return $data;
    }

    /**
     * Prepare personal option values
     *
     * @param array $option
     */
    private function preparePersonalOptionValues(&$option)
    {
        if (!isset($option['values']) || !is_array($option['values'])) {
            return;
        }
        foreach ($option['values'] as &$value) {
            $optionValueLabels = [];
            if (isset($value['labels']) && is_array($value['labels'])) {
                foreach ($value['labels'] as $storeId => $title) {
                    if (empty($title)) {
                        continue;
                    }
                    $optionValueLabels[] = [
                        'store_id' => $storeId,
                        'title' => $title
                    ];
                }
            }
            $value['labels'] = $optionValueLabels;
        }
    }
}
