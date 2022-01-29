<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Product\Type\EventTicket\Resolver;

use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver\RequireShipping;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class RequireShippingTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Product\Type\EventTicket\Resolver
 */
class RequireShippingTest extends TestCase
{
    /**
     * @var RequireShipping
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->createPartialMock(Config::class, ['isTicketRequireShipping']);
        $this->model = $objectManager->getObject(
            RequireShipping::class,
            [
                'config' => $this->configMock
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @param $requireShipping
     * @param $configRequireShipping
     * @param $expected
     * @dataProvider requireShippingDataProvider
     */
    public function testResolve($requireShipping, $configRequireShipping, $expected)
    {
        $productMock = $this->createPartialMock(Product::class, ['getTypeInstance']);
        $productTypeMock = $this->createPartialMock(EventTicket::class, ['isRequireShipping']);

        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($productTypeMock);
        $productTypeMock->expects($this->once())
            ->method('isRequireShipping')
            ->with($productMock)
            ->willReturn($requireShipping);

        if (null === $requireShipping) {
            $this->configMock->expects($this->once())
                ->method('isTicketRequireShipping')
                ->willReturn($configRequireShipping);
        }

        $this->assertEquals($expected, $this->model->resolve($productMock));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function requireShippingDataProvider()
    {
        return [
            [null, true, true],
            [null, false, false],
            [true, null, true],
            [false, null, false],
        ];
    }
}
