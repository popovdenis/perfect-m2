<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels;

use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabels\Repository;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var StorefrontLabelsEntityInterface $entity */
        $this->repository->save($entity);

        return $entity;
    }
}
