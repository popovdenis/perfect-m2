<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\PersonalOption;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOptionRepository;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation\PersonalOption
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var HydratorPool
     */
    private $hydratorPool;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var PersonalOptionRepository
     */
    private $personalOptionRepository;

    /**
     * @param HydratorPool $hydratorPool
     * @param DataObjectProcessor $dataObjectProcessor
     * @param PersonalOptionRepository $personalOptionRepository
     */
    public function __construct(
        HydratorPool $hydratorPool,
        DataObjectProcessor $dataObjectProcessor,
        PersonalOptionRepository $personalOptionRepository
    ) {
        $this->hydratorPool = $hydratorPool;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->personalOptionRepository = $personalOptionRepository;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getTypeId() !== EventTicket::TYPE_CODE) {
            return $entity;
        }

        $options = $this->personalOptionRepository->getByProductId($entity->getId(), $entity->getStoreId());
        $extension = $entity->getExtensionAttributes();
        $extension->setAwEtPersonalOptions($options);
        $entity->setExtensionAttributes($extension);

        $hydrator = $this->hydratorPool->getHydrator(ProductInterface::class);
        $entityData = $hydrator->extract($entity);
        $entityData[ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS] = $this->extractOptions($options);
        $entity = $hydrator->hydrate($entity, $entityData);
        return $entity;
    }

    /**
     * Extract options
     *
     * @param array $options
     * @return array
     */
    private function extractOptions($options)
    {
        $extractedOptions = [];
        foreach ($options as $option) {
            $extractedOptions[] = $this->dataObjectProcessor->buildOutputDataArray(
                $option,
                ProductPersonalOptionInterface::class
            );
        }
        return $extractedOptions;
    }
}
