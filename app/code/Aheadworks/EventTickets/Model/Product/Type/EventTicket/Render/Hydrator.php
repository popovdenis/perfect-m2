<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render;

use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\EntityManager\Hydrator as EntityManagerHydrator;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class Hydrator
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render
 */
class Hydrator implements HydratorInterface
{
    /**
     * @var EntityManagerHydrator
     */
    private $entityManagerHydrator;

    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @param EntityManagerHydrator $entityManagerHydrator
     * @param SimpleDataObjectConverter $simpleDataObjectConverter
     */
    public function __construct(
        EntityManagerHydrator $entityManagerHydrator,
        SimpleDataObjectConverter $simpleDataObjectConverter
    ) {
        $this->entityManagerHydrator = $entityManagerHydrator;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        $entityArray = $this->entityManagerHydrator->extract($entity);
        return $this->simpleDataObjectConverter->convertStdObjectToArray($entityArray);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        return $this->entityManagerHydrator->hydrate($entity, $data);
    }
}
