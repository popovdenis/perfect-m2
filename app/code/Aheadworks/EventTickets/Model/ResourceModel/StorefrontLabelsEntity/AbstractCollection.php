<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity;

use Aheadworks\EventTickets\Model\ResourceModel\AbstractCollection as BaseAbstractCollection;
use Aheadworks\EventTickets\Model\StorefrontLabelsResolver;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Magento\Framework\Api\SortOrder;

/**
 * Class AbstractCollection
 * @package Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity
 */
abstract class AbstractCollection extends BaseAbstractCollection
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StorefrontLabelsResolver
     */
    protected $storefrontLabelsResolver;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param StorefrontLabelsResolver $storefrontLabelsResolver
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        StorefrontLabelsResolver $storefrontLabelsResolver,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storefrontLabelsResolver = $storefrontLabelsResolver;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Set store id for entity labels retrieving
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get store id for entity labels retrieving
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachLabels();
        $this->addCurrentLabels();
        return parent::_afterLoad();
    }

    /**
     * Attach labels on storefront per store view
     *
     * @return $this
     */
    protected function attachLabels()
    {
        return $this->attachRelationTable(
            'aw_et_label',
            $this->getIdFieldName(),
            'entity_id',
            [
                StorefrontLabelsInterface::TITLE,
                StorefrontLabelsInterface::DESCRIPTION,
                StorefrontLabelsInterface::STORE_ID
            ],
            StorefrontLabelsEntityInterface::LABELS,
            [
                [
                    'field' => 'entity_type',
                    'condition' => '=',
                    'value' => $this->getStorefrontLabelsEntityType()
                ]
            ],
            [
                'field' => StorefrontLabelsInterface::STORE_ID,
                'direction' => SortOrder::SORT_ASC
            ],
            true
        );
    }

    /**
     * Retrieve type of entity with storefront labels
     *
     * @return string
     */
    abstract protected function getStorefrontLabelsEntityType();

    /**
     * Add labels on storefront for specific store view
     *
     * @return $this
     */
    protected function addCurrentLabels()
    {
        $currentStoreId = $this->getStoreId();
        if (isset($currentStoreId)) {
            foreach ($this as $item) {
                $labelsData = $item->getData(StorefrontLabelsEntityInterface::LABELS);
                if (is_array($labelsData)) {
                    $currentLabelsRecord = $this->storefrontLabelsResolver
                        ->getLabelsForStoreAsArray($labelsData, $currentStoreId);
                    $item->setData(StorefrontLabelsEntityInterface::CURRENT_LABELS, $currentLabelsRecord);
                }
            }
        }
        return $this;
    }
}
