<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Convert\DataObject as DataObjectConverter;

/**
 * Class Select
 *
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute
 */
class Select extends AbstractSource
{
    /**
     * @var string
     */
    protected $idField = 'id';

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var AttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @param MetadataPool $metadataPool
     * @param AttributeFactory $eavAttributeFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param DataObjectConverter $dataObjectConverter
     */
    public function __construct(
        MetadataPool $metadataPool,
        AttributeFactory $eavAttributeFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        DataObjectConverter $dataObjectConverter
    ) {
        $this->metadataPool = $metadataPool;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $optionsArray = $this->getOptionsArray();
            $this->_options = $this->getOptionsFromArray($optionsArray);
        }
        return $this->_options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (strpos($value, ',') !== false) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getSpecificOptions($value);

        if ($isMultiple) {
            $values = [];
            foreach ($options as $item) {
                if (in_array($item['value'], $value)) {
                    $values[] = $item['label'];
                }
            }
            return $values;
        }

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve Option values array by ids
     *
     * @param string|array $ids
     * @return array
     */
    protected function getSpecificOptions($ids)
    {
        $optionsArray = $this->getOptionsArray($ids);
        $options = $this->getOptionsFromArray($optionsArray);

        return $options;
    }

    /**
     * Retrieve options as array of objects
     *
     * @param string|array|null $ids
     * @return array
     */
    protected function getOptionsArray($ids = null)
    {
        return [];
    }

    /**
     * Retrieve options array from the array of spaces
     *
     * @param StorefrontLabelsEntityInterface[] $spacesArray
     * @return array
     */
    protected function getOptionsFromArray($spacesArray)
    {
        $callable = function (StorefrontLabelsEntityInterface $item) {
            $optionLabel = '';
            $currentLabels = $item->getCurrentLabels();
            if ($currentLabels instanceof StorefrontLabelsInterface) {
                $optionLabel = $currentLabels->getTitle();
            }
            return $optionLabel;
        };

        return $this->dataObjectConverter->toOptionArray($spacesArray, $this->idField, $callable);
    }
}
