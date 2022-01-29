<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabels\Repository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\EventTickets\Model\StorefrontLabelsResolver;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StorefrontLabelsInterfaceFactory
     */
    private $storefrontLabelsFactory;

    /**
     * @var StorefrontLabelsResolver
     */
    private $storefrontLabelsResolver;

    /**
     * @param Repository $repository
     * @param DataObjectHelper $dataObjectHelper
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     * @param StorefrontLabelsResolver $storefrontLabelsResolver
     */
    public function __construct(
        Repository $repository,
        DataObjectHelper $dataObjectHelper,
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory,
        StorefrontLabelsResolver $storefrontLabelsResolver
    ) {
        $this->repository = $repository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
        $this->storefrontLabelsResolver = $storefrontLabelsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var StorefrontLabelsEntityInterface $entity */
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $labelsData = $this->repository->get($entity);
        $labelsRecordsArray = $this->getLabelObjects($labelsData);
        $currentLabelsRecord = $this->storefrontLabelsResolver->getLabelsForStore(
            $labelsRecordsArray,
            $arguments['store_id']
        );
        $entity
            ->setLabels($labelsRecordsArray)
            ->setCurrentLabels($currentLabelsRecord);

        return $entity;
    }

    /**
     * Retrieve storefront labels from data array
     *
     * @param array $labelsData
     * @return StorefrontLabelsInterface[]
     */
    protected function getLabelObjects($labelsData)
    {
        $labelsRecordsArray = [];
        foreach ($labelsData as $labelsDataRow) {
            /** @var StorefrontLabelsInterface $labelsRecord */
            $labelsRecord = $this->storefrontLabelsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $labelsRecord,
                $labelsDataRow,
                StorefrontLabelsInterface::class
            );
            $labelsRecordsArray[] = $labelsRecord;
        }
        return $labelsRecordsArray;
    }
}
