<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\EventTickets\Model\ResourceModel\Sector as ResourceSector;
use Aheadworks\EventTickets\Model\Sector\Validator;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Aheadworks\EventTickets\Api\SpaceRepositoryInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;

/**
 * Class Sector
 *
 * @package Aheadworks\EventTickets\Model
 */
class Sector extends AbstractModel implements SectorInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var SpaceRepositoryInterfaceFactory
     */
    private $spaceRepositoryFactory;

    /**
     * @var SpaceInterface
     */
    private $space;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Validator $validator
     * @param SpaceRepositoryInterfaceFactory $spaceRepositoryFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Validator $validator,
        SpaceRepositoryInterfaceFactory $spaceRepositoryFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->spaceRepositoryFactory = $spaceRepositoryFactory;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceSector::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketsQty()
    {
        return $this->getData(self::TICKETS_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setTicketsQty($ticketsQty)
    {
        return $this->setData(self::TICKETS_QTY, $ticketsQty);
    }

    /**
     * {@inheritdoc}
     */
    public function getImagePath()
    {
        return $this->getData(self::IMAGE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function setImagePath($imagePath)
    {
        return $this->setData(self::IMAGE_PATH, $imagePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpaceId()
    {
        return $this->getData(self::SPACE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpaceId($spaceId)
    {
        return $this->setData(self::SPACE_ID, $spaceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        return $this->getData(self::LABELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabels($labelsRecordsArray)
    {
        return $this->setData(self::LABELS, $labelsRecordsArray);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLabels()
    {
        return $this->getData(self::CURRENT_LABELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLabels($labelsRecord)
    {
        return $this->setData(self::CURRENT_LABELS, $labelsRecord);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\SectorExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorefrontLabelsEntityType()
    {
        return self::STOREFRONT_LABELS_ENTITY_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->validateBeforeSave();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * Retrieve parent space object
     *
     * @param int|null $storeId
     * @return SpaceInterface|null
     */
    public function getSpace($storeId = null)
    {
        if (empty($this->space)) {
            /** @var SpaceRepositoryInterface $spaceRepository */
            $spaceRepository = $this->spaceRepositoryFactory->create();
            try {
                /** @var SpaceInterface space */
                $this->space = $spaceRepository->get($this->getSpaceId(), $storeId);
            } catch (\Exception $exception) {
            }
        }
        return $this->space;
    }
}
