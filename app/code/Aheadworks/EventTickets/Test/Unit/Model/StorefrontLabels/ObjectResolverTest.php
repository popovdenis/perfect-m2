<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\StorefrontLabels;

use Aheadworks\EventTickets\Model\StorefrontLabels\ObjectResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ObjectResolverTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\StorefrontLabels
 */
class ObjectResolverTest extends TestCase
{
    /**
     * @var ObjectResolver
     */
    private $model;

    /**
     * @var StorefrontLabelsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storefrontLabelsFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->storefrontLabelsFactoryMock = $this->createPartialMock(
            StorefrontLabelsInterfaceFactory::class,
            ['create']
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            ['populateWithArray']
        );
        $this->model = $objectManager->getObject(
            ObjectResolver::class,
            [
                'storefrontLabelsFactory' => $this->storefrontLabelsFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @param array|StorefrontLabelsInterface $label
     * @param StorefrontLabelsInterface $expected
     * @dataProvider resolveDataProvider
     */
    public function testResolve($label, $expected)
    {
        if (is_array($label)) {
            $this->storefrontLabelsFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($expected);
            $this->dataObjectHelperMock->expects($this->once())
                ->method('populateWithArray')
                ->with($expected, $label, StorefrontLabelsInterface::class);
        }

        $this->assertEquals($expected, $this->model->resolve($label));
    }

    /**
     * Data provider for resolve
     *
     * @return array
     */
    public function resolveDataProvider()
    {
        $labelMock = $this->getMockForAbstractClass(StorefrontLabelsInterface::class);
        return [
            [
                $labelMock,
                $labelMock
            ],
            [
                [
                    StorefrontLabelsInterface::STORE_ID => 1,
                    StorefrontLabelsInterface::TITLE => 'title',
                ],
                $labelMock
            ]
        ];
    }
}
