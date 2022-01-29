<?php
namespace Aheadworks\EventTickets\Test\Unit\Model;

use Aheadworks\EventTickets\Model\Url;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Url\ParamEncryptor;

/**
 * Class UrlTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model
 */
class UrlTest extends TestCase
{
    /**
     * @var Url
     */
    private $model;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var ParamEncryptor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encryptorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->encryptorMock = $this->createPartialMock(ParamEncryptor::class, ['encrypt']);
        $this->model = $objectManager->getObject(
            Url::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'encryptor' => $this->encryptorMock
            ]
        );
    }

    /**
     * Test getEncryptUrl method
     */
    public function testGetEncryptUrl()
    {
        $route = 'aw_event_tickets/ticket/management';
        $params = ['ticket_number' => 'number'];
        $expected = 'http://mydomain.com/aw_event_tickets/ticket/management';
        $key = 'encryptor_key';

        $this->encryptorMock->expects($this->any())
            ->method('encrypt')
            ->willReturn($key);
        $this->urlBuilderMock->expects($this->any())
            ->method('getUrl')
            ->with($route, ['key' => $key])
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEncryptUrl($route, $params));
    }
}
