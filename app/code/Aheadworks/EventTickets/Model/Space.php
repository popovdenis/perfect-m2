<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Model\Space\Validator;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\EventTickets\Model\ResourceModel\Space as ResourceSpace;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Aheadworks\EventTickets\Api\VenueRepositoryInterfaceFactory;
use Aheadworks\EventTickets\Api\Data\VenueInterface;

/**
 * Class Space
 *
 * @package Aheadworks\EventTickets\Model
 */
class Space extends AbstractModel implements SpaceInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var VenueRepositoryInterfaceFactory
     */
    private $venueRepositoryFactory;

    /**
     * @var VenueInterface
     */
    private $venue;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Validator $validator
     * @param VenueRepositoryInterfaceFactory $venueRepositoryFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Validator $validator,
        VenueRepositoryInterfaceFactory $venueRepositoryFactory,
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
        $this->validator = $validator;
        $this->venueRepositoryFactory = $venueRepositoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceSpace::class);
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
    public function getVenueId()
    {
        return $this->getData(self::VENUE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setVenueId($venueId)
    {
        return $this->setData(self::VENUE_ID, $venueId);
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
    public function getSectors()
    {
        return $this->getData(self::SECTORS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectors($sectors)
    {
        return $this->setData(self::SECTORS, $sectors);
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
        \Aheadworks\EventTickets\Api\Data\SpaceExtensionInterface $extensionAttributes
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
     * Retrieve parent venue object
     *
     * @param int|null $storeId
     * @return VenueInterface|null
     */
    public function getVenue($storeId = null)
    {
        if (empty($this->venue)) {
            /** @var VenueRepositoryInterface $venueRepository */
            $venueRepository = $this->venueRepositoryFactory->create();
            try {
                /** @var VenueInterface space */
                $this->venue = $venueRepository->get($this->getVenueId(), $storeId);
            } catch (\Exception $exception) {
            }
        }
        return $this->venue;
    }
}
