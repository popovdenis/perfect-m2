<?php
namespace Aheadworks\EventTickets\Block\Adminhtml\Product\Edit\Tab;

use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Magento\Backend\Block\Template;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Class SpaceConfiguration
 *
 * @package Aheadworks\EventTickets\Block\Adminhtml\Product\Edit\Tab
 */
class SpaceConfiguration extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_EventTickets::catalog/product/new/space/configuration.phtml';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @param Context $context
     * @param LocatorInterface $locator
     * @param array $data
     */
    public function __construct(
        Context $context,
        LocatorInterface $locator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->locator = $locator;
    }

    /**
     * Retrieve form name
     *
     * @return string
     */
    public function getForm()
    {
        return $this->getData('config/form');
    }

    /**
     * Retrieve scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->getForm() . '.' . $this->getData('config/modal');
    }

    /**
     * Retrieve step wizard name
     *
     * @return string
     */
    public function getStepWizardName()
    {
        return $this->getData('config/stepWizardName');
    }

    /**
     * Retrieve step wizard url
     *
     * @return string
     */
    public function getStepWizardUrl()
    {
        return $this->getUrl($this->getData('config/stepWizardUrl'), ['id' => $this->getProduct()->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->isAvailableRender()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Check if available render block
     *
     * @return bool
     */
    protected function isAvailableRender()
    {
        return $this->getProduct()->getTypeId() == EventTicket::TYPE_CODE;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return ProductInterface
     */
    private function getProduct()
    {
        return $this->locator->getProduct();
    }
}
