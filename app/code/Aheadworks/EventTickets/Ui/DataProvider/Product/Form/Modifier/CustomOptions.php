<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class CustomOptions
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Product\Form\Modifier
 */
class CustomOptions extends AbstractModifier
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
            $this->modifyCustomOptionsFieldset($meta);
        }

        return $meta;
    }

    /**
     * Modify custom options fieldset
     *
     * @param array $meta
     * @return $this
     */
    private function modifyCustomOptionsFieldset(&$meta)
    {
        $customOptionsContainerPath = $this->arrayManager->findPath('custom_options', $meta, null, 'children');
        if (!$customOptionsContainerPath) {
            return $this;
        }

        $meta = $this->arrayManager->merge(
            $customOptionsContainerPath,
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'visible' => 0,
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }
}
