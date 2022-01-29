<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Product\Option;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Model\Product\Option\Render;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Model\Product\Option\Renderer\Composite;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;

/**
 * Class RenderTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Product\Option
 */
class RenderTest extends TestCase
{
    /**
     * @var Render
     */
    private $model;

    /**
     * @var OptionExtractor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionExtractorMock;

    /**
     * @var Composite|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rendererProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->optionExtractorMock = $this->createPartialMock(OptionExtractor::class, ['extractFromArray']);
        $this->rendererProcessorMock = $this->createPartialMock(Composite::class, ['render']);
        $optionsConfig = [
            'ticket_code' => [
                'optionName' => OptionInterface::TICKET_NUMBERS,
                'sections' => [
                    Render::BACKEND_SECTION
                ]
            ]
        ];
        $this->model = $objectManager->getObject(
            Render::class,
            [
                'optionExtractor' => $this->optionExtractorMock,
                'rendererProcessor' => $this->rendererProcessorMock,
                'optionsConfig' => $optionsConfig
            ]
        );
    }

    /**
     * Test render method
     *
     * @param array $data
     * @param Product $product
     * @param string $section
     * @param mixed $expected
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $product, $section, $expected)
    {
        $objectOptionsMock = $this->getMockForAbstractClass(OptionInterface::class);
        $this->optionExtractorMock->expects($this->once())
            ->method('extractFromArray')
            ->with($expected, $product)
            ->willReturn($objectOptionsMock);

        $this->rendererProcessorMock->expects($this->once())
            ->method('render')
            ->with($objectOptionsMock)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->render($data, $product, $section));
    }

    /**
     * Data provider for render test
     *
     * @return array
     */
    public function renderDataProvider()
    {
        return [
            [
                [
                    OptionInterface::TICKET_NUMBERS => ['number1', 'number2'],
                    OptionInterface::SECTOR_ID => 1,
                    OptionInterface::TICKET_TYPE_ID => 1,
                    OptionInterface::AMOUNT => 10,
                    OptionInterface::OPTION_ATTENDEE_IDS => '',
                    OptionInterface::ATTENDEES => '',
                    OptionInterface::ATTENDEE_IDS => ''
                ],
                null,
                Render::GLOBAL_SECTION,
                [
                    OptionInterface::TICKET_NUMBERS => ['number1', 'number2'],
                    OptionInterface::SECTOR_ID => 1,
                    OptionInterface::TICKET_TYPE_ID => 1,
                    OptionInterface::AMOUNT => 10,
                    OptionInterface::OPTION_ATTENDEE_IDS => '',
                    OptionInterface::ATTENDEES => '',
                    OptionInterface::ATTENDEE_IDS => ''
                ]
            ],
            [
                [
                    OptionInterface::TICKET_NUMBERS => ['number1', 'number2'],
                    OptionInterface::SECTOR_ID => 1,
                    OptionInterface::TICKET_TYPE_ID => 1,
                    OptionInterface::AMOUNT => 10,
                    OptionInterface::OPTION_ATTENDEE_IDS => '',
                    OptionInterface::ATTENDEES => '',
                    OptionInterface::ATTENDEE_IDS => ''
                ],
                null,
                Render::BACKEND_SECTION,
                [
                    OptionInterface::TICKET_NUMBERS => ['number1', 'number2'],
                    OptionInterface::SECTOR_ID => 1,
                    OptionInterface::TICKET_TYPE_ID => 1,
                    OptionInterface::AMOUNT => 10,
                    OptionInterface::OPTION_ATTENDEE_IDS => '',
                    OptionInterface::ATTENDEES => '',
                    OptionInterface::ATTENDEE_IDS => ''
                ]
            ],
            [
                [
                    OptionInterface::TICKET_NUMBERS => ['number1', 'number2'],
                    OptionInterface::SECTOR_ID => 1,
                    OptionInterface::TICKET_TYPE_ID => 1,
                    OptionInterface::AMOUNT => 10,
                    OptionInterface::OPTION_ATTENDEE_IDS => '',
                    OptionInterface::ATTENDEES => '',
                    OptionInterface::ATTENDEE_IDS => ''
                ],
                null,
                Render::FRONTEND_SECTION,
                [
                    OptionInterface::SECTOR_ID => 1,
                    OptionInterface::TICKET_TYPE_ID => 1,
                    OptionInterface::AMOUNT => 10,
                    OptionInterface::OPTION_ATTENDEE_IDS => '',
                    OptionInterface::ATTENDEES => '',
                    OptionInterface::ATTENDEE_IDS => ''
                ]
            ]
        ];
    }
}
