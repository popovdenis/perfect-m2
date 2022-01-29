<?php
namespace Aheadworks\EventTickets\Api\Data\ProductTypeRender;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AdditionalProductRenderInterface
 * @package Aheadworks\EventTickets\Api\Data\ProductTypeRender
 */
interface AdditionalProductRenderInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const KEY = 'key';
    const QTY = 'qty';
    const SKU = 'sku';
    const NAME = 'name';
    const SHORT_DESCRIPTION = 'short_description';
    const TYPE = 'type';
    const IS_SALABLE = 'is_salable';
    const URL = 'url';
    const STORE_ID = 'store_id';
    const CURRENCY_CODE = 'currency_code';
    const OPTION = 'option';
    const PRICE_INFO = 'price_info';
    const IMAGE = 'image';
    /**#@-*/

    /**
     * Get product id
     *
     * @return int
     */
    public function getId();

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setId($productId);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key);

    /**
     * Get qty
     *
     * @return float
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get product name
     *
     * @return string
     */
    public function getName();

    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get short description
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription($shortDescription);

    /**
     * Get product type
     *
     * @return string
     */
    public function getType();

    /**
     * Set product type
     *
     * @param string $productType
     * @return $this
     */
    public function setType($productType);

    /**
     * Get is salable
     *
     * @return string
     */
    public function getIsSalable();

    /**
     * Set is salable
     *
     * @param string $isSalable
     * @return $this
     */
    public function setIsSalable($isSalable);

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode();

    /**
     * Set currency code
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode);

    /**
     * Get option
     *
     * @return string
     */
    public function getOption();

    /**
     * Set option
     *
     * @param string $option
     * @return $this
     */
    public function setOption($option);

    /**
     * Get price info
     *
     * @return \Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface
     */
    public function getPriceInfo();

    /**
     * Set price info
     *
     * @param \Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface $priceInfo
     * @return $this
     */
    public function setPriceInfo($priceInfo);

    /**
     * Get image
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProduct\ImageInterface
     */
    public function getImage();

    /**
     * Set image
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProduct\ImageInterface $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderExtensionInterface $extAttr
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderExtensionInterface $extAttr
    );
}
