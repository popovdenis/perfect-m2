<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\TicketSellingDeadline;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\ConfigFactory;
use Magento\Catalog\Model\Config;

/**
 * Class Common
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter
 */
class Common implements ConverterInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ConfigFactory $catalogConfigFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigFactory $catalogConfigFactory
    ) {
        $this->storeManager = $storeManager;
        $this->catalogConfig = $catalogConfigFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function convertRow($row)
    {
        $data = [
            'website_ids' => [$this->storeManager->getDefaultStoreView()->getWebsiteId()],
            'store_id' => Store::DEFAULT_STORE_ID,
            ProductInterface::NAME => $row[ProductInterface::NAME],
            ProductInterface::SKU => $row[ProductInterface::SKU],
            ProductInterface::ATTRIBUTE_SET_ID => $this->catalogConfig->getAttributeSetId(4, $row['attribute_set']),
            ProductInterface::TYPE_ID => EventTicket::TYPE_CODE,
            ProductInterface::STATUS => Status::STATUS_DISABLED,
            ProductInterface::VISIBILITY => Visibility::VISIBILITY_BOTH,
            ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE => TicketSellingDeadline::EVENT_START_DATE,
            ProductAttributeInterface::CODE_AW_ET_START_DATE => $row[ProductAttributeInterface::CODE_AW_ET_START_DATE],
            ProductAttributeInterface::CODE_AW_ET_END_DATE => $row[ProductAttributeInterface::CODE_AW_ET_END_DATE]
        ];

        return $data;
    }
}
