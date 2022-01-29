<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\PersonalOption;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOptionRepository;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\PersonalOption
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var PersonalOptionRepository
     */
    private $personalOptionRepository;

    /**
     * @param PersonalOptionRepository $personalOptionRepository
     */
    public function __construct(
        PersonalOptionRepository $personalOptionRepository
    ) {
        $this->personalOptionRepository = $personalOptionRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getTypeId() !== EventTicket::TYPE_CODE) {
            return $entity;
        }

        $entityId = $entity->getId();
        $personalOptions = !empty($entity->getExtensionAttributes()->getAwEtPersonalOptions())
            ? $entity->getExtensionAttributes()->getAwEtPersonalOptions()
            : [];
        $this->personalOptionRepository->deleteByProductId($entityId);
        $this->personalOptionRepository->save($personalOptions, $entityId);

        return $entity;
    }
}
