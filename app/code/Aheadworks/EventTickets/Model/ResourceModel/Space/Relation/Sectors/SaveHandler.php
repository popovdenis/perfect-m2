<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Space\Relation\Sectors;

use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\EventTickets\Model\ResourceModel\Space\Relation\Sectors
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @param SectorRepositoryInterface $sectorRepository
     */
    public function __construct(
        SectorRepositoryInterface $sectorRepository
    ) {
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var SpaceInterface $entity */
        if ($entityId = (int)$entity->getId()) {
            /** @var SectorInterface[] $sectors */
            $sectors = $entity->getSectors();
            if (is_array($sectors)) {
                /** @var SectorInterface $sector */
                foreach ($sectors as $sector) {
                    if ($sector instanceof SectorInterface) {
                        if (empty($sector->getSpaceId())) {
                            $sector->setSpaceId($entityId);
                        }
                        $this->sectorRepository->save($sector);
                    }
                }
            }
        }
        return $entity;
    }
}
