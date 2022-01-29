<?php
namespace Aheadworks\EventTickets\Model\Source\Product\Attribute;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Data\Collection;

/**
 * Class TicketSellingDeadline
 *
 * @package Aheadworks\EventTickets\Model\Source\Product\Attribute
 */
class TicketSellingDeadline extends AbstractSource
{
    /**#@+
     * Ticket statuses
     */
    const EVENT_START_DATE = 1;
    const EVENT_END_DATE = 2;
    const CUSTOM_DATE = 3;
    /**#@-*/

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeFactory
     */
    private $eavAttributeFactory;

    /**
     * @param MetadataPool $metadataPool
     * @param AttributeFactory $eavAttributeFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        AttributeFactory $eavAttributeFactory
    ) {
        $this->metadataPool = $metadataPool;
        $this->eavAttributeFactory = $eavAttributeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::EVENT_START_DATE, 'label' => __('Event Start Date')],
                ['value' => self::EVENT_END_DATE, 'label' => __('Event End Date')],
                ['value' => self::CUSTOM_DATE, 'label' => __('Custom Date')]
            ];
        }
        return $this->_options;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => true,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aheadworks Event Tickets Tickets Selling Deadline',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->eavAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * {@inheritdoc}
     */
    public function addValueSortToCollection($collection, $dir = Collection::SORT_ORDER_DESC)
    {
        $linkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();

        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        $tableName = $attributeCode . '_t';
        $collection->getSelect()
            ->joinLeft(
                [$tableName => $attributeTable],
                "e.{$linkField}={$tableName}.{$linkField}" .
                " AND {$tableName}.attribute_id='{$attributeId}'" .
                " AND {$tableName}.store_id='0'",
                []
            );
        $collection->getSelect()->order($tableName . '.value ' . $dir);
        return $this;
    }
}
