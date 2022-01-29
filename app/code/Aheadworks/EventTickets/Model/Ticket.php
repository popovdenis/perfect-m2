<?php
namespace Aheadworks\EventTickets\Model;

use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\Status;
use Aheadworks\EventTickets\Model\Ticket\Validator;
use Magento\Catalog\Model\Product;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket as ResourceTicket;
use Aheadworks\EventTickets\Model\Email\AttachmentInterface;
use Aheadworks\EventTickets\Model\Ticket\Pdf as TicketPdf;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\Options as OptionsResolver;

/**
 * Class Ticket
 *
 * @package Aheadworks\EventTickets\Model
 */
class Ticket extends AbstractModel implements TicketInterface, IdentityInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var TicketPdf
     */
    private $ticketPdf;

    /**
     * @var AttachmentInterface
     */
    private $pdf;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Validator $validator
     * @param TicketPdf $ticketPdf
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Validator $validator,
        TicketPdf $ticketPdf,
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
        $this->ticketPdf = $ticketPdf;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceTicket::class);
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
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getNumber()
    {
        return $this->getData(self::NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setNumber($number)
    {
        return $this->setData(self::NUMBER, $number);
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
    public function getEmailSent()
    {
        return $this->getData(self::EMAIL_SENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailSent($emailSent)
    {
        return $this->setData(self::EMAIL_SENT, $emailSent);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketTypeId()
    {
        return $this->getData(self::TICKET_TYPE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTicketTypeId($ticketTypeId)
    {
        return $this->setData(self::TICKET_TYPE_ID, $ticketTypeId);
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
    public function getSectorId()
    {
        return $this->getData(self::SECTOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectorId($sectorId)
    {
        return $this->setData(self::SECTOR_ID, $sectorId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePrice()
    {
        return $this->getData(self::BASE_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePrice($basePrice)
    {
        return $this->setData(self::BASE_PRICE, $basePrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseOriginalPrice()
    {
        return $this->getData(self::BASE_ORIGINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseOriginalPrice($baseOriginalPrice)
    {
        return $this->setData(self::BASE_ORIGINAL_PRICE, $baseOriginalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectorStorefrontTitle()
    {
        return $this->getData(self::SECTOR_STOREFRONT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSectorStorefrontTitle($title)
    {
        return $this->setData(self::SECTOR_STOREFRONT_TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketTypeStorefrontTitle()
    {
        return $this->getData(self::TICKET_TYPE_STOREFRONT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTicketTypeStorefrontTitle($title)
    {
        return $this->setData(self::TICKET_TYPE_STOREFRONT_TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventTitle()
    {
        return $this->getData(self::EVENT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventTitle($title)
    {
        return $this->setData(self::EVENT_TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventAddress()
    {
        return $this->getData(self::EVENT_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventAddress($address)
    {
        return $this->setData(self::EVENT_ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDescription()
    {
        return $this->getData(self::EVENT_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDescription($description)
    {
        return $this->setData(self::EVENT_DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventStartDate()
    {
        return $this->getData(self::EVENT_START_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventStartDate($startDate)
    {
        return $this->setData(self::EVENT_START_DATE, $startDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventEndDate()
    {
        return $this->getData(self::EVENT_END_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventEndDate($endDate)
    {
        return $this->setData(self::EVENT_END_DATE, $endDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventImage()
    {
        return $this->getData(self::EVENT_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventImage($image)
    {
        return $this->setData(self::EVENT_IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurringTimeSlotId()
    {
        return $this->getData(self::RECURRING_TIME_SLOT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecurringTimeSlotId($id)
    {
        return $this->setData(self::RECURRING_TIME_SLOT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        return $this->setData(self::OPTIONS, $options);
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
        \Aheadworks\EventTickets\Api\Data\TicketExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
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
    public function getIdentities()
    {
        $identities = [];
        if (in_array($this->getStatus(), [Status::PENDING, Status::CANCELED])) {
            $identities[] = Product::CACHE_TAG . '_' . $this->getProductId();
        }

        return $identities;
    }

    /**
     * Retrieve ticket pdf view
     *
     * @param bool $forDownload
     * @return \Aheadworks\EventTickets\Model\Email\AttachmentInterface
     */
    public function getPdf($forDownload = false)
    {
        if (empty($this->pdf)) {
            $this->pdf = $this->ticketPdf->getTicketPdf($this, $forDownload);
        }
        return $this->pdf;
    }

    /**
     * Resolve and retrieve attendee name
     *
     * @return string
     */
    public function getAttendeeName()
    {
        $attendeeName = $this->getOptionByType(ProductPersonalOptionInterface::OPTION_TYPE_NAME);

        return empty($attendeeName) ? $this->getCustomerName() : $attendeeName;
    }

    /**
     * Resolve and retrieve attendee email
     *
     * @return string
     */
    public function getAttendeeEmail()
    {
        $attendeeEmail = $this->getOptionByType(ProductPersonalOptionInterface::OPTION_TYPE_EMAIL);

        return empty($attendeeEmail) ? $this->getCustomerEmail() : $attendeeEmail;
    }

    /**
     * Resolve attendee from ticket options
     *
     * @param string $optionType
     * @return string|null
     */
    public function getOptionByType($optionType)
    {
        $options = $this->getOptions() ? : [];
        foreach ($options as $option) {
            if ($option->getType() == $optionType) {
                return $option->getValue() == OptionsResolver::DEFAULT_OPTION_VALUE ? null : $option->getValue();
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
