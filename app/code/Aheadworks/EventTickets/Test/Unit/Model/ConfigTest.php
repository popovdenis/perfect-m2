<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Source\Ticket\NumberFormat;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test getOrderStatusForTicketCreation method
     */
    public function testGetOrderStatusForTicketCreation()
    {
        $expected = 'complete';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_CREATE_TICKET_BY_ORDER_STATUS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getOrderStatusForTicketCreation());
    }

    /**
     * Test getTicketManagementGroupOnStorefront method
     */
    public function testGetTicketManagementGroupOnStorefront()
    {
        $expected = 'group_code';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_TICKET_MANAGEMENT_GROUP_ON_STOREFRONT)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getTicketManagementGroupOnStorefront());
    }

    /**
     * Test getUrlToEventsCategory method
     */
    public function testGetUrlToEventsCategory()
    {
        $expected = 'url';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_URL_TO_EVENTS_CATEGORY)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getUrlToEventsCategory());
    }

    /**
     * Test isTicketRequireShipping method
     */
    public function testIsTicketRequireShipping()
    {
        $expected = true;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_TICKET_REQUIRE_SHIPPING)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isTicketRequireShipping());
    }

    /**
     * Test isPastEventsMustBeHidden method
     */
    public function testIsPastEventsMustBeHidden()
    {
        $expected = true;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_HIDE_PAST_EVENTS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isPastEventsMustBeHidden());
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $storeId = 1;
        $expected = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSender($storeId));
    }

    /**
     * Testing of getSenderName method
     */
    public function testGetSenderName()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_name';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/name', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getSenderName($storeId));
    }

    /**
     * Testing of getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_email';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/email', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getSenderEmail($storeId));
    }

    /**
     * Test getNewTicketEmailTemplate method
     */
    public function testGetNewTicketEmailTemplate()
    {
        $storeId = 1;
        $expected = 'template_id';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_TICKET_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewTicketEmailTemplate($storeId));
    }

    /**
     * Test getTicketTemplatePdf method
     */
    public function testGetTicketTemplatePdf()
    {
        $storeId = 1;
        $expected = 'template_id';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_TICKET_TEMPLATE_PDF, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getTicketTemplatePdf($storeId));
    }

    /**
     * Testing of getTicketNumberLength method
     */
    public function testGetTicketNumberLength()
    {
        $websiteId = 1;
        $expectedValue = 12;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_PATTERN_NUMBER_LENGTH, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getTicketNumberLength($websiteId));
    }

    /**
     * Testing of getTicketNumberPrefix method
     */
    public function testGetTicketNumberPrefix()
    {
        $websiteId = 1;
        $expectedValue = 'aw';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_PATTERN_NUMBER_PREFIX, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getTicketNumberPrefix($websiteId));
    }

    /**
     * Testing of getTicketNumberFormat method
     */
    public function testGetTicketNumberFormat()
    {
        $websiteId = 1;
        $expectedValue = NumberFormat::ALPHANUMERIC;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_PATTERN_NUMBER_FORMAT, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getTicketNumberFormat($websiteId));
    }

    /**
     * Testing of getTicketNumberSuffix method
     */
    public function testGetTicketNumberSuffix()
    {
        $websiteId = 1;
        $expectedValue = 'aw';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_PATTERN_NUMBER_SUFFIX, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getTicketNumberSuffix($websiteId));
    }

    /**
     * Testing of getTicketNumberDashAtEvery method
     */
    public function testGetTicketNumberDashAtEvery()
    {
        $websiteId = 1;
        $expectedValue = 2;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_PATTERN_NUMBER_DASH_EVERY_X_CHARACTERS, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getTicketNumberDashAtEvery($websiteId));
    }
}
