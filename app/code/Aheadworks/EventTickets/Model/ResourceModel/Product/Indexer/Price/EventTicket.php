<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Indexer\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\DefaultPrice;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Indexer\Price
 */
class EventTicket extends DefaultPrice
{
    /**
     * {@inheritdoc}
     */
    protected function reindex($entityIds = null)
    {
        if ($this->hasEntity() || !empty($entityIds)) {
            $this->_prepareFinalPriceData($entityIds);
            $this->_movePriceDataToIndexTable();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSelect($entityIds = null, $type = null)
    {
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['e' => $this->getTable('catalog_product_entity')],
            'entity_id'
        )->join(
            ['cg' => $this->getTable('customer_group')],
            '',
            ['customer_group_id']
        );
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns(
            'website_id',
            'cw'
        )->joinLeft(
            ['awetps' => $this->getTable('aw_et_product_sector')],
            'e.entity_id = awetps.product_id',
            []
        )->joinLeft(
            ['awetpst' => $this->getTable('aw_et_product_sector_tickets')],
            'awetps.id = awetpst.product_sector_id',
            [
            ]
        )->group(
            ['e.entity_id', 'cg.customer_group_id', 'cw.website_id']
        )->where(
            'e.type_id=?',
            $type
        );

        if ($this->moduleManager->isEnabled('Magento_Tax')) {
            $taxClassId = $this->_addAttributeToSelect(
                $select,
                'tax_class_id',
                'e.' . $metadata->getLinkField(),
                'cs.store_id'
            );
        } else {
            $taxClassId = new \Zend_Db_Expr('0');
        }
        $select->columns(
            [
                'tax_class_id' => $taxClassId,
                'orig_price' => new \Zend_Db_Expr('NULL'),
                'price' => new \Zend_Db_Expr('NULL'),
                'min_price' => new \Zend_Db_Expr('MIN(awetpst.price)'),
                'max_price' => new \Zend_Db_Expr('MAX(awetpst.price)'),
                'tier_price' => new \Zend_Db_Expr('NULL'),
                'base_price' => new \Zend_Db_Expr('NULL'),
            ]
        );

        if ($entityIds !== null) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
        return $select;
    }
}
