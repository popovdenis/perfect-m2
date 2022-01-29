<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Ticket\Email;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Aheadworks\EventTickets\Model\Ticket\Email\UrlBuilder;

/**
 * Class UrlBuilderTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Ticket\Email
 */
class UrlBuilderTest extends TestCase
{
    /**
     * @var UrlBuilder
     */
    private $object;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $frontendUrlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->frontendUrlBuilderMock = $this->getMockForAbstractClass(
            UrlInterface::class
        );

        $this->object = $objectManager->getObject(
            UrlBuilder::class,
            [
                'frontendUrlBuilder' => $this->frontendUrlBuilderMock,
            ]
        );
    }

    /**
     * Testing of getUrl method
     */
    public function testGetUrl()
    {
        $routePath = 'route_path';
        $scope = 'scope_value';
        $params = [];

        $expected = 'store_url/route_path/params';

        $this->frontendUrlBuilderMock->expects($this->once())
            ->method('setScope')
            ->with($scope)
            ->willReturnSelf();

        $this->frontendUrlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($routePath, $params)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getUrl($routePath, $scope, $params));
    }
}
