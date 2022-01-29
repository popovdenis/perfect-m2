<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Order\Items\Column\Name;

use Magento\Sales\Block\Adminhtml\Items\Column\Name as SalesColumnName;
use Magento\Backend\Block\Template\Context;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\OptionFactory;
use Aheadworks\EventTickets\Model\Product\Option\Render as OptionRender;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Order\Items\Column\Name
 */
class EventTicket extends SalesColumnName
{
    /**
     * @var OptionRender
     */
    private $optionRender;

    /**
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param OptionFactory $optionFactory
     * @param OptionRender $optionRender
     * @param array $data
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry $registry,
        OptionFactory $optionFactory,
        OptionRender $optionRender,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $optionFactory,
            $data
        );
        $this->optionRender = $optionRender;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderOptions()
    {
        return $this->optionRender->render($this->getItem()->getProductOptions());
    }
}
