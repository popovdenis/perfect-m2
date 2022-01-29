<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOption\Relation\OptionValue;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionValueInterface;
use Aheadworks\EventTickets\Model\Product\PersonalOptions\Config as PersonalOptionsConfig;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\PersonalOption\Relation\OptionValue
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PersonalOptionsConfig
     */
    private $personalOptionsConfig;

    /**
     * @param EntityManager $entityManager
     * @param PersonalOptionsConfig $personalOptionsConfig
     */
    public function __construct(
        EntityManager $entityManager,
        PersonalOptionsConfig $personalOptionsConfig
    ) {
        $this->entityManager = $entityManager;
        $this->personalOptionsConfig = $personalOptionsConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($this->isAllowSaveOptionValues($entity)) {
            $this->saveOptionValues($entity);
        }

        return $entity;
    }

    /**
     * Check if allow save option values
     *
     * @param ProductPersonalOptionInterface $entity
     * @return bool
     */
    private function isAllowSaveOptionValues($entity)
    {
        $selectTypeOptions = $this->personalOptionsConfig
            ->getTypesByGroup(ProductPersonalOptionInterface::OPTION_GROUP_SELECT);

        return in_array($entity->getType(), $selectTypeOptions);
    }

    /**
     * Save option values
     *
     * @param ProductPersonalOptionInterface $entity
     * @return void
     * @throws CouldNotSaveException
     */
    private function saveOptionValues($entity)
    {
        $values = $entity->getValues();
        /** @var ProductPersonalOptionValueInterface $value */
        foreach ($values as $value) {
            try {
                $value->setId(null)->setOptionId($entity->getId());
                $this->entityManager->save($value);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('Could not save option values.'));
            }
        }
    }
}
