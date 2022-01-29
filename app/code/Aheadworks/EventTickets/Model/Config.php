<?php
namespace Aheadworks\EventTickets\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\EventTickets\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_CREATE_TICKET_BY_ORDER_STATUS = 'aw_event_tickets/general/create_ticket_by_order_status';
    const XML_PATH_GENERAL_TICKET_MANAGEMENT_GROUP_ON_STOREFRONT =
        'aw_event_tickets/general/ticket_management_group_on_storefront';
    const XML_PATH_GENERAL_TICKET_REQUIRE_SHIPPING = 'aw_event_tickets/general/ticket_require_shipping';
    const XML_PATH_GENERAL_URL_TO_EVENTS_CATEGORY = 'aw_event_tickets/general/url_to_events_category';
    const XML_PATH_GENERAL_HIDE_PAST_EVENTS = 'aw_event_tickets/general/hide_past_events';
    const XML_PATH_EMAIL_SENDER = 'aw_event_tickets/email/sender';
    const XML_PATH_EMAIL_NEW_TICKET_TEMPLATE = 'aw_event_tickets/email/new_ticket_template';
    const XML_PATH_EMAIL_TICKET_TEMPLATE_PDF = 'aw_event_tickets/email/ticket_template_pdf';
    const XML_PATH_PATTERN_NUMBER_LENGTH = 'aw_event_tickets/number_pattern/number_length';
    const XML_PATH_PATTERN_NUMBER_FORMAT = 'aw_event_tickets/number_pattern/number_format';
    const XML_PATH_PATTERN_NUMBER_PREFIX = 'aw_event_tickets/number_pattern/number_prefix';
    const XML_PATH_PATTERN_NUMBER_SUFFIX = 'aw_event_tickets/number_pattern/number_suffix';
    const XML_PATH_PATTERN_NUMBER_DASH_EVERY_X_CHARACTERS = 'aw_event_tickets/number_pattern/dash_every_x_characters';
    const XML_PATH_GENERAL_LOCALE_CODE = 'general/locale/code';
    const XML_PATH_GENERAL_LOCALE_TIMEZONE = 'general/locale/timezone';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve order status for ticket creation
     *
     * @return string
     */
    public function getOrderStatusForTicketCreation()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CREATE_TICKET_BY_ORDER_STATUS
        );
    }

    /**
     * Check if ticket require shipping
     *
     * @return bool
     */
    public function isTicketRequireShipping()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_TICKET_REQUIRE_SHIPPING
        );
    }

    /**
     * Retrieve customer group for ticket management on storefront
     *
     * @return string
     */
    public function getTicketManagementGroupOnStorefront()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_TICKET_MANAGEMENT_GROUP_ON_STOREFRONT
        );
    }

    /**
     * Retrieve url to events category
     *
     * @param int|null $websiteId
     * @return string|null
     */
    public function getUrlToEventsCategory($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_URL_TO_EVENTS_CATEGORY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if past events should be hidden
     *
     * @return bool
     */
    public function isPastEventsMustBeHidden()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_HIDE_PAST_EVENTS
        );
    }

    /**
     * Retrieve email sender
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderName($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender email
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderEmail($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new ticket email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewTicketEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_TICKET_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new ticket email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTicketTemplatePdf($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TICKET_TEMPLATE_PDF,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve ticket number length
     *
     * @param int|null $websiteId
     * @return int
     */
    public function getTicketNumberLength($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PATTERN_NUMBER_LENGTH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve ticket number format
     *
     * @param string|null $websiteId
     * @return string
     */
    public function getTicketNumberFormat($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PATTERN_NUMBER_FORMAT,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve ticket number prefix
     *
     * @param string|null $websiteId
     * @return int
     */
    public function getTicketNumberPrefix($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PATTERN_NUMBER_PREFIX,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve ticket number suffix
     *
     * @param string|null $websiteId
     * @return int
     */
    public function getTicketNumberSuffix($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PATTERN_NUMBER_SUFFIX,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve ticket number dash every X characters
     *
     * @param int|null $websiteId
     * @return int
     */
    public function getTicketNumberDashAtEvery($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PATTERN_NUMBER_DASH_EVERY_X_CHARACTERS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }
}
