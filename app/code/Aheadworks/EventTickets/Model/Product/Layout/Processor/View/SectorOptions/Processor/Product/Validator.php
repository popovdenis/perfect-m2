<?php
namespace Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Processor\Product
 */
class Validator
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if product is valid
     *
     * @param ProductInterface $product
     * @return  bool
     */
    public function isValid($product)
    {
        return $this->isEnabled($product)
            && $this->isDisplayed($product)
            && $this->isAllowedForCurrentWebsite($product);
    }

    /**
     * Check if out of stock product can be visible
     *
     * @param ProductInterface $product
     * @return  bool
     */
    private function isDisplayed($product)
    {
        $isShowOutOfStock = $this->scopeConfig->getValue(
            Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            ScopeInterface::SCOPE_STORE
        );

        return !(!$product->getIsSalable() && !$isShowOutOfStock);
    }

    /**
     * Check if product is enabled
     *
     * @param ProductInterface $product
     * @return  bool
     */
    private function isEnabled($product)
    {
        return $product->getStatus() == ProductStatus::STATUS_ENABLED;
    }

    /**
     * Check if product is allowed for current website
     *
     * @param ProductInterface $product
     * @return  bool
     */
    private function isAllowedForCurrentWebsite($product)
    {
        $result = false;
        try {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $result = in_array($websiteId, $product->getWebsiteIds());
        } catch (NoSuchEntityException $exception) {
        }

        return $result;
    }
}
