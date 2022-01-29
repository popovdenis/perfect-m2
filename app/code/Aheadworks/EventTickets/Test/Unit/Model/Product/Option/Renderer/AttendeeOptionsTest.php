<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Product\Option\Renderer;

use Aheadworks\EventTickets\Api\Data\AttendeeInterface;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Model\Product\Option\Renderer\AttendeeOptions;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Escaper;

/**
 * Class AttendeeOptionsTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Product\Option\Renderer
 */
class AttendeeOptionsTest extends TestCase
{
    /**
     * @var AttendeeOptions
     */
    private $model;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->escaperMock = $this->createPartialMock(Escaper::class, ['escapeHtml']);

        $this->model = $objectManager->getObject(
            AttendeeOptions::class,
            [
                'escaper' => $this->escaperMock
            ]
        );
    }

    /**
     * Test render method
     */
    public function testRender()
    {
        $attendeeData = [
            'id' => 1,
            'value' => 'value',
            'label' => 'label'
        ];
        $attendeeOptionMock = $this->getMockForAbstractClass(AttendeeInterface::class);
        $attendeeOptions = [$attendeeOptionMock];
        $optionsMock = $this->getMockForAbstractClass(OptionInterface::class);
        $optionsMock->expects($this->exactly(2))
            ->method('getAwEtAttendees')
            ->willReturn($attendeeOptions);

        $attendeeOptionMock->expects($this->once())
            ->method('getAttendeeId')
            ->willReturn($attendeeData['id']);
        $attendeeOptionMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn($attendeeData['value']);
        $attendeeOptionMock->expects($this->once())
            ->method('getLabel')
            ->willReturn($attendeeData['label']);

        $result = $this->model->render($optionsMock);
        $this->assertTrue(is_array($result) && !empty($result));
    }

    /**
     * Test render method, attendee options is empty
     */
    public function testRenderAttendeeOptionsIsEmpty()
    {
        $expected = [];
        $attendeeOptions = [];
        $optionsMock = $this->getMockForAbstractClass(OptionInterface::class);
        $optionsMock->expects($this->once())
            ->method('getAwEtAttendees')
            ->willReturn($attendeeOptions);

        $this->assertEquals($expected, $this->model->render($optionsMock));
    }
}
