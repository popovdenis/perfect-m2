<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection
    as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
    as ProductCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Product;

class Loader
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Load the list of available products with given ids for the specific store
     *
     * @param string $storeId
     * @param int[] $productIdList
     * @return Product[]
     * @throws LocalizedException
     */
    public function getProductList(string $storeId, array $productIdList): array
    {
        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->setStore($storeId)
            ->addIdFilter($productIdList)
            ->addStoreFilter()
            ->addAttributeToSelect('*')
            ->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner'
            )->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner'
            )->addOptionsToResult();

        return $productCollection->getItems();
    }
}
