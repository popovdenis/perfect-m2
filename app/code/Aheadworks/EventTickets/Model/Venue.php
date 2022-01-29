<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\EventTickets\Model\ResourceModel\Venue as ResourceVenue;
use Aheadworks\EventTickets\Model\Venue\Validator;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Venue
 *
 * @package Aheadworks\EventTickets\Model
 */
class Venue extends AbstractModel implements VenueInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Validator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Validator $validator,
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
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceVenue::class);
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
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getCoordinates()
    {
        return $this->getData(self::COORDINATES);
    }

    /**
     * {@inheritdoc}
     */
    public function setCoordinates($coordinates)
    {
        return $this->setData(self::COORDINATES, $coordinates);
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
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\VenueExtensionInterface $extensionAttributes
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
}
