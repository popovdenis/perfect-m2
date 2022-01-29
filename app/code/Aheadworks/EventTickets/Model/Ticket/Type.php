<?php
namespace Aheadworks\EventTickets\Model\Ticket;

use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type as ResourceTicketType;
use Aheadworks\EventTickets\Model\Ticket\Type\Validator;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Type
 *
 * @package Aheadworks\EventTickets\Model\Ticket
 */
class Type extends AbstractModel implements TicketTypeInterface
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
        $this->_init(ResourceTicketType::class);
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
        \Aheadworks\EventTickets\Api\Data\TicketTypeExtensionInterface $extensionAttributes
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
