<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class StockData
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class StockData extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === EventTicket::TYPE_CODE) {
            $this
                ->modifyQtyField($meta)
                ->modifyAdvancedInventory($meta);
        }

        return $meta;
    }

    /**
     * Modify qty field
     *
     * @param array $meta
     * @return $this
     */
    private function modifyQtyField(&$meta)
    {
        $qtyContainerPath = $this->arrayManager->findPath('quantity_and_stock_status_qty', $meta, null, 'children');
        if (!$qtyContainerPath) {
            return $this;
        }

        $meta = $this->arrayManager->merge(
            $qtyContainerPath . '/children/qty',
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'disabled' => true,
                            'default' => 0
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * Modify advanced inventory
     *
     * @param array $meta
     * @return $this
     */
    private function modifyAdvancedInventory(&$meta)
    {
        $configForInvisibleComponent = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'visible' => 0,
                        'imports' => [
                            'visible' => null,
                        ],
                    ]
                ],
            ],
        ];

        $meta['advanced_inventory_modal'] = [
            'children' => [
                'stock_data' => [
                    'children' => [
                        'container_manage_stock' => $configForInvisibleComponent,
                        'qty' => $configForInvisibleComponent,
                        'container_min_qty' => $configForInvisibleComponent,
                        'is_qty_decimal' => $configForInvisibleComponent,
                        'is_decimal_divided' => $configForInvisibleComponent,
                        'container_backorders' => $configForInvisibleComponent,
                        'container_notify_stock_qty' => $configForInvisibleComponent,
                        'container_enable_qty_increments' => $configForInvisibleComponent,
                        'container_qty_increments' => $configForInvisibleComponent,
                        'container_is_in_stock' => $configForInvisibleComponent,
                    ],
                ],
            ],
        ];

        return $this;
    }
}
