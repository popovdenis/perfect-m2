<?php
namespace Aheadworks\EventTickets\Plugin\Model\ResourceModel;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOptionRepository;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product;
use Aheadworks\EventTickets\Model\ResourceModel\Product\SectorRepository as ProductSectorRepository;

/**
 * Class ProductPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\Model\ResourceModel
 */
class ProductPlugin
{
    /**
     * @var ProductSectorRepository
     */
    private $productSectorRepository;

    /**
     * @var PersonalOptionRepository
     */
    private $personalOptionRepository;

    /**
     * @var Product
     */
    private $productResource;

    /**
     * @param ProductSectorRepository $productSectorRepository
     * @param PersonalOptionRepository $personalOptionRepository
     * @param Product $productResource
     */
    public function __construct(
        ProductSectorRepository $productSectorRepository,
        PersonalOptionRepository $personalOptionRepository,
        Product $productResource
    ) {
        $this->productSectorRepository = $productSectorRepository;
        $this->personalOptionRepository = $personalOptionRepository;
        $this->productResource = $productResource;
    }

    /**
     * Save product
     *
     * @param Product $subject
     * @param \Closure $proceed
     * @param ProductInterface $object
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave($subject, \Closure $proceed, $object)
    {
        if ($object->getTypeId() == EventTicket::TYPE_CODE) {
            $this->disableManageStock($object);
            if (!$object->getEntityId()) {
                $object->setPageLayout('1column');
            }
        }

        return $proceed($object);
    }

    /**
     * Delete product and associated entities
     *
     * @param Product $subject
     * @param \Closure $proceed
     * @param ProductInterface $object
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function aroundDelete($subject, \Closure $proceed, $object)
    {
        $this->productResource->beginTransaction();
        try {
            $result = $proceed($object);
            $this->removeRelatedProductData($object);
            $this->productResource->commit();
        } catch (\Exception $e) {
            $this->productResource->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Remove related product data
     *
     * @param ProductInterface $object
     * @throws \Exception
     */
    private function removeRelatedProductData($object)
    {
        if ($object->getTypeId() == EventTicket::TYPE_CODE) {
            $productId = $object->getId();
            $this->productSectorRepository->deleteByProductId($productId);
            $this->personalOptionRepository->deleteByProductId($productId);
        }
    }

    /**
     * Disable manage stock in product
     *
     * @param ProductInterface $object
     * @return $this
     */
    private function disableManageStock(&$object)
    {
        $etStockDataConfig = [
            'use_config_manage_stock' => 0,
            'manage_stock' => 0
        ];
        $stockData = $object->getStockData();
        if (is_array($stockData)) {
            $stockData = array_merge($stockData, $etStockDataConfig);
        } else {
            $stockData = $etStockDataConfig;
        }
        $object->setStockData($stockData);

        return $this;
    }
}
