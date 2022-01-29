<?php
namespace Aheadworks\EventTickets\Model\Import\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Aheadworks\EventTickets\Model\Import\Processor\Composite as ImportProcessor;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Model\Import\Product\Type
 */
class EventTicket extends AbstractType
{
    /**
     * @var ImportProcessor
     */
    private $importProcessor;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    protected $_disabledAttrs = [
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
        ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE
    ];

    /**
     * @param AttributeSetCollectionFactory $attrSetColFac
     * @param ProductAttributeCollectionFactory $prodAttrColFac
     * @param ResourceConnection $resource
     * @param ImportProcessor $importProcessor
     * @param ProductRepositoryInterface $productRepository
     * @param array $params
     * @param MetadataPool|null $metadataPool
     * @throws LocalizedException
     */
    public function __construct(
        AttributeSetCollectionFactory $attrSetColFac,
        ProductAttributeCollectionFactory $prodAttrColFac,
        ResourceConnection $resource,
        ImportProcessor $importProcessor,
        ProductRepositoryInterface $productRepository,
        array $params,
        MetadataPool $metadataPool = null
    ) {
        $this->importProcessor = $importProcessor;
        $this->productRepository = $productRepository;
        parent::__construct(
            $attrSetColFac,
            $prodAttrColFac,
            $resource,
            $params,
            $metadataPool
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        if (in_array($attrCode, $this->_disabledAttrs)) {
            $flag = false;
        } else {
            $flag = parent::_isAttributeRequiredCheckNeeded($attrCode);
        }
        return $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function saveData()
    {
        $newSku = $this->_entityModel->getNewSku();
        while ($bunch = $this->_entityModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->_entityModel->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }
                $rowSku = strtolower($rowData[ImportProduct::COL_SKU]);
                $productData = $newSku[$rowSku];
                if ($this->_type != $productData['type_id']) {
                    continue;
                }

                $entity = $this->productRepository->getById($productData['entity_id']);
                $this->importProcessor->processData($rowData, $entity);
            }
        }

        return $this;
    }

    /**
     * Retrieve attribute from cache
     *
     * @param string $attributeCode
     * @return array
     */
    public function retrieveAttributeFromCache($attributeCode)
    {
        $attribute = parent::retrieveAttributeFromCache($attributeCode);
        if (in_array($attributeCode, $this->_disabledAttrs)) {
            $attribute['is_required'] = '0';
        }

        return $attribute;
    }
}
