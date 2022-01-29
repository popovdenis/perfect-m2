<?php
namespace Aheadworks\EventTickets\Observer;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config as PageConfig;

/**
 * Class AddCssForSwatchesObserver
 * @package Aheadworks\EventTickets\Observer
 */
class AddCssForSwatchesObserver implements ObserverInterface
{
    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param PageConfig $pageConfig
     * @param ProductMetadataInterface $productMetadata
     * @param RequestInterface $request
     * @param Registry $registry
     */
    public function __construct(
        PageConfig $pageConfig,
        ProductMetadataInterface $productMetadata,
        RequestInterface $request,
        Registry $registry
    ) {
        $this->pageConfig = $pageConfig;
        $this->productMetadata = $productMetadata;
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $product = $this->registry->registry('product');
        if (version_compare($magentoVersion, '2.3.0', '<')
            && $this->request->getModuleName() == 'catalog'
            && $this->request->getActionName() == 'view'
            && $product
            && $product->getTypeId() == EventTicket::TYPE_CODE
        ) {
            $this->pageConfig->addPageAsset('Magento_Swatches::css/swatches.css');
        }
    }
}
