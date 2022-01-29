<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Product;

use Aheadworks\EventTickets\Model\Product\Configuration;
use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Product\Option\Render as OptionRender;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;

/**
 * Class ConfigurationTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Product
 */
class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $model;

    /**
     * @var OptionRender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionRenderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->optionRenderMock = $this->createPartialMock(OptionRender::class, ['render']);
        $this->model = $objectManager->getObject(
            Configuration::class,
            [
                'optionRender' => $this->optionRenderMock
            ]
        );
    }

    /**
     * Test getOptions method
     *
     * @param Item $options
     * @param array $expected
     * @dataProvider optionsDataProvider
     */
    public function testGetOptions($options, $expected)
    {
        $itemMock = $this->createPartialMock(Item::class, ['getOptionsByCode', 'getProduct']);
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);

        $itemMock->expects($this->any())
            ->method('getOptionsByCode')
            ->willReturn($options);
        $itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $this->optionRenderMock->expects($this->once())
            ->method('render')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getOptions($itemMock));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function optionsDataProvider()
    {
        $options = [
            1 => [
                'input' => [
                    'code' => 'code1',
                    'value' => 'value1'
                ],
                'output' => [
                    'label' => 'label1',
                    'value' => 'value1'
                ]
            ],
            2 => [
                'input' => [
                    'code' => 'code2',
                    'value' => 'value2'
                ],
                'output' => [
                    'label' => 'label2',
                    'value' => 'value2'
                ]
            ]
        ];
        $optionMock1 = $this->createPartialMock(Option::class, ['getCode', 'getValue']);
        $optionMock2 = $this->createPartialMock(Option::class, ['getCode', 'getValue']);
        $optionMock1->expects($this->any())
            ->method('getCode')
            ->willReturn($options[1]['input']['code']);
        $optionMock1->expects($this->any())
            ->method('getValue')
            ->willReturn($options[1]['input']['value']);
        $optionMock2->expects($this->any())
            ->method('getCode')
            ->willReturn($options[2]['input']['code']);
        $optionMock2->expects($this->any())
            ->method('getValue')
            ->willReturn($options[2]['input']['value']);

        return [
            [
                null, []
            ],
            [
                [$optionMock1, $optionMock2], [0 => $options[1]['output'], 1 => $options[2]['output']]
            ],
            [
                [$optionMock1, $optionMock2], [0 => $options[1]['output']]
            ]
        ];
    }
}
